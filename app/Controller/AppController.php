<?php
App::uses('Controller', 'Controller');
App::uses('Setting', 'Model');
App::uses('AdvancedFilter', 'Model');
App::uses('AdvancedFilterUserSetting', 'Model');
App::uses('ErambaCakeEmail', 'Network/Email');
App::uses('Hash', 'Utility');
App::uses('Inflector', 'Utility');
App::uses('CrudControllerTrait', 'Crud.Lib');
App::uses('CrudAction', 'Crud.Controller/Crud');
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');
App::uses('FieldDataCollection', 'FieldData.Model/FieldData');
App::uses('SystemHealthLib', 'Lib');
App::uses('AutoUpdateLib', 'Lib');
App::uses('AclCheck', 'Lib/Acl');

class AppController extends Controller {

	use CrudControllerTrait;

	public $uses = array('Setting', 'LdapConnectorAuthentication', 'Notification');
	
	// Use $_appControllerConfigDefaults['components'] instead
	public $components = [];
	// Use $_appControllerConfigDefaults['helpers'] instead
	public $helpers = [];

	protected $logged = null;

	public $title = null;
	public $subTitle = null;

	public $modelLabel = null;

	// compatibility after removing mappingcomponent
	private $recordsHandled = false;

	public $_disableCsrfCheck = false;

	// This variable tells on which portal is user currently working
	protected $portal = 'main';

	/**
	 * Instance of a FieldDataCollection class for the current model.
	 * 
	 * @var FieldDataCollection
	 */
	protected $_FieldDataCollection = null;

	/**
	 * Use this property for editing default configuration of AppController. Child controllers can overwrite these settings with $_appControllerConfig property
	 */
	protected $_appControllerConfigDefaults = [
		'components' => [
			'AppConfig',
			'Session',
			'Cookie',
			'Translations.Translations',
			'Auth' => array(
				'className' => 'AppAuth',
				'authorize' => array(
					'AppActions' => array('actionPath' => 'controllers/')
				)
			),
			'RequestHandler', 'AppSession', 'Flash',
			'DebugKit.Toolbar' => [
				'panels' => [
					'DebugKit.History' => false,
					'DebugKit.Variables' => false,
					'AppVariables',
					'DebugKit.Include' => false,
					'FieldData.FieldData',
					'AppCrud'
				],
				'cache' => [
					'duration' => '+10 minutes'
				]
			], 'Acl', 'AppAcl', 'Menu', 'SamlAuth', 'OauthGoogleAuth',
			'ConcurrentEdit.ConcurrentEdit',
			'Crud.Crud' => [
				'listeners' => [
					'Crud.DebugKit'
				],
				// Actions disabled by default
				'actions' => [
					'index' => [
						'enabled' => false,
						'className' => 'AppIndex',
						'viewVar' => 'data',
						'contain' => []
					],
					'add' => [
						'enabled' => false,
						'className' => 'AppAdd',
					],
					'edit' => [
						'enabled' => false,
						'className' => 'AppEdit',
					],
					'delete' => [
						'enabled' => false,
						'className' => 'AppDelete',
					],
					'trash' => [
						'enabled' => false,
						'className' => 'Trash.Trash',
					],
					'history' => [
						'className' => 'ObjectVersion.History',
						'enabled' => false
					],
					'restore' => [
						'className' => 'ObjectVersion.Restore',
						'enabled' => false
					]
				],
				'listeners' => [
					'Section' => [
						'className' => 'Section'
					],
					'Toolbar' => [
						'className' => 'Toolbar'
					],
					'InlineReload' => [
						'className' => 'AdvancedFilters.InlineReload'
					],
					'InlineEdit' => [
						'className' => 'InlineEdit.InlineEdit'
					],
					'FormReload' => [
						'className' => 'FormReload'
					],
					'ImportTool.ImportTool',
					'QuickAdd' => [
						'className' => 'QuickAdd.QuickAdd'
					],
					'EditedColumn' => [
						'className' => 'EditedColumn'
					],
					'CustomLabels' => [
						'className' => 'CustomLabels.CustomLabels'
					],
					'SectionInfo' => [
						'className' => 'SectionInfo.SectionInfo'
					],
					'Community' => [
						'className' => 'Community'
					]
				]
			],
			'Ajax',
			'YoonityJSConnector.YoonityJSConnector',
			'Modals.Modals',
			'Breadcrumbs',
			'AppNotification.AppNotifications'
		],
		'helpers' => [
			'Html', 'Form', 'Video', 'Ux', 'FieldData.FieldData', 'Visualisation.Visualisation', 'LimitlessTheme.ContentPanels', 'UserFields.UserField', 'FieldData.FieldDataCollection', 'FieldData.FieldDataRenderer', 'AdvancedFilters.AdvancedFilterRenderer', 'AdvancedFilters.AdvancedFilterPagination', 'AdvancedFilters.AdvancedFilters', 'LimitlessTheme.Tables', 'LimitlessTheme.Buttons', 'LimitlessTheme.Icons', 'LimitlessTheme.Alerts', 'LimitlessTheme.Popovers', 'LimitlessTheme.Labels', 'Content', 'LimitlessTheme.PageToolbar', 'LimitlessTheme.LayoutToolbar', 'LimitlessTheme.ItemDropdown', 'ObjectRenderer.ObjectRenderer', 'ObjectStatus.ObjectStatus', 'ObjectVersion.ObjectVersionAudit', 'Widget.Widget'
		],
		'elements' => [
			'initMenu' => true
		]
	];
	
	/**
	 * Use this property in child controller for editing configuration of AppController
	 *
	 * Turn off any component by components => [componentName => false];
	 * Example 1: You want to preconfigure some component, but don't want to use it in all child controllers. Just type here components => [componentName => false]
	 * Example 2: You want to add configuration of any preconfigured component. You can do it like this: components => [componentName => newConfigurationArray]
	 * Example 3: You want to change configuration of any preconfigured component. You need to disable preconfigured component first (Example 1) and then use components property to add newly configured component to your controller
	 *
	 * Turn off or on any feature, which is presented in elements array in $_appControllerConfigDefaults property
	 * Example 1: You want to turn on/off auto-set viewOptions of FieldDataEntity into viewVars of view. You can do it like this: elements => [FieldDataEntity => [viewOptions => false/true (Default is false)]]
	 */
	protected $_appControllerConfig = [
	];

	public function __construct($request = null, $response = null)
	{
		//
		// Apply configuration for AppController from its child classes
		$this->_appControllerConfig = Hash::merge($this->_appControllerConfigDefaults, $this->_appControllerConfig);
		$this->_applyConfigOnComponents();
		$this->_applyConfigOnHelpers();
		$this->_applyConfigOnElements();
		//

		parent::__construct($request, $response);
	}

	/**
	 * Apply configuration for AppController's components (which are loaded automatically in AppController) from its child class
	 * @return void
	 */
	protected function _applyConfigOnComponents()
	{
		$appConConfDefComponents = Hash::normalize($this->_appControllerConfigDefaults['components'], true);
		$appConConfComponents = Hash::normalize($this->_appControllerConfig['components'], true);
		$components = Hash::normalize($this->components, true);
		foreach ($appConConfDefComponents as $component => $settings) {
			if (array_key_exists($component, $appConConfComponents) && $appConConfComponents[$component] === false) {
				unset($appConConfDefComponents[$component]);
				unset($appConConfComponents[$component]);
			}
		}

		$components = Hash::merge($appConConfComponents, $components);
		$this->components = Hash::merge($appConConfDefComponents, $components);
	}

	/**
	 * Apply configuration for AppController's helpers (which are loaded automatically in AppController) from its child class
	 * @return void
	 */
	protected function _applyConfigOnHelpers()
	{
		$appConConfDefHelpers = Hash::normalize($this->_appControllerConfigDefaults['helpers'], true);
		$appConConfHelpers = Hash::normalize($this->_appControllerConfig['helpers'], true);
		$helpers = Hash::normalize($this->helpers, true);
		foreach ($appConConfDefHelpers as $helper => $settings) {
			if (array_key_exists($helper, $appConConfHelpers) && $appConConfHelpers[$helper] === false) {
				unset($appConConfDefHelpers[$helper]);
				unset($appConConfHelpers[$helper]);
			}
		}

		$helpers = Hash::merge($appConConfHelpers, $helpers);
		$this->helpers = Hash::merge($appConConfDefHelpers, $helpers);
	}

	/**
	 * Apply configuration for AppController's elements (settings for properties and methods) from its child class
	 * @return void
	 */
	protected function _applyConfigOnElements()
	{
		// Empty, use this function for any of element's (AppController's properties and methods) initial configuration
	}

	/**
	 * Set configuration to $_appControllerConfig property
	 * @param array $config Configuration in format: ['elements' => ['nameInDepth1' => ['nameInDepth2' => true]]]
	 */
	protected function _setAppControllerConfig(Array $config, $rewrite = false)
	{
		$acceptableKeys = array_keys($this->_appControllerConfigDefaults);
		foreach ($config as $key => $val) {
			if (!in_array($key, $acceptableKeys)) {
				unset($config[$key]);
			}
		}

		if ($rewrite) {
			foreach ($config as $key => $val) {
				$this->_appControllerConfig[$key] = $val;
			}
		} else {
			$this->_appControllerConfig = Hash::merge($this->_appControllerConfig, $config);
		}
	}

	/**
	 * Get config from any of deep level of $_appControllerConfig property
	 * Function accepts any number of arguments. Example: _getAppControllerConfig('elements', 'nameInDepth1', 'nameInDepth2');
	 * @return mixed config
	 */
	protected function _getAppControllerConfig()
	{
		$args = func_get_args();
		$tempConfig = $this->_appControllerConfig;
		foreach ($args as $arg) {
			if (array_key_exists($arg, $tempConfig)) {
				$tempConfig = $tempConfig[$arg];
			} else {
				$tempConfig = false;
				break;
			}
		}

		return $tempConfig;
	}

	/**
	 * Extended method for constructing classes.
	 */
	public function constructClasses()
	{
		parent::constructClasses();

		// until all Session->setFlash() -related syntax is moved to Flash Component, we have to replace it,
		// because SessionComponent was causing issues on the latest CakePHP v2.10.0 in conjunction with FlashHelper
		$loaded = $this->Components->set('Session', $this->AppSession);
		$this->Session = $loaded['Session'];
		return true;
	}

	/**
	 * Generate Security salt.
	 * 
	 * @return boolean
	 */
	protected function _generateSecuritySalt()
	{
		if (!SystemHealthLib::salt()) {
			$salt = CakeText::uuid();
			if (!$this->Setting->updateVariable('SECURITY_SALT', $salt)) {
				return false;
			}

			Configure::write('Security.salt', $salt);
		}
	}
	
	public function beforeFilter()
	{
		//
		// Initial core configuration for all controllers
		//
		
		// delete this information from session because i18n is checking for this infromation and we dont want it because of translations
		if ($this->Session->check('Config.language')) {
			$this->Session->delete('Config.language');
		}

		$this->_generateSecuritySalt();

		// configure cookies
		$this->_setupDefaultCookies();

		$this->AppConfig->ensureOffloadSSL();

		$this->_setupSecurity();

		// Setup common authentication settings
		$this->_setupCommonAuthenticationSettings();

		if ($this->request->is('api') && !$this->request->is('ajax')) {
			$this->_apiAuthInit();
		} else {
			$this->_setupAuthentication();
		}

		$this->_afterSetup();

		$this->_checkAccountReady();
		$this->_checkDefaultPasswordChange();
// debug($this->Auth->user());
		$this->Crud->removeListener('RelatedModels');

		// Model-related class variables
		if (method_exists($this->_model(), 'label')) {
			$this->modelLabel = $this->_model()->label(['singular' => true]);
		}
		
		if ($this->Crud->getSubject()->model->hasMethod('getFieldCollection')) {
			$this->_FieldDataCollection = $this->Crud->getSubject()->model->getFieldCollection();
		}

		$this->Crud->on('beforeRender', function(CakeEvent $event)
		{
			$this->_beforeRenderHandler($event);
		}, [
			'priority' => 90
		]);

		// set acl instance to AclCheck lib
		AclCheck::setAclInstance($this->Acl->adapter());
	}

	/**
	 * Triggers only crud action handle execution.
	 */
	protected function handleCrudAction($action, $args = array())
	{
		if (empty($args)) {
			$args = $this->request->params['pass'];
		}
		$subject = $this->Crud->trigger('beforeHandle', compact('args', 'action'));

		return $this->Crud->action($action)->handle($subject);
	}

	/**
	 * Get the current referenced model instance.
	 * 
	 * @return Model
	 */
	protected function _model()
	{
		return $this->{$this->modelClass};
	}

	public function _beforeRenderHandler(CakeEvent $event)
	{
		$this->_setLayoutVars($event);
		$this->_setCommonVars($event);
	}

	protected function _setLayoutVars(CakeEvent $event)
	{
		//
		// By default all data needed for add/edit form is set through Field Data layer
		// This sets all input variables as well as $FieldDataCollection class instance
		if (!empty($this->_FieldDataCollection) &&
			!empty($this->_model()->Behaviors) &&
			$this->_model()->Behaviors->enabled('FieldData.FieldData')) {
			$this->set($this->_FieldDataCollection->getViewOptions());
		}
		//
		
		$this->_formatTitle();
		$this->_setBreadcrumbs($event);

		if ($this->Auth->loggedIn()) {
			// Handles only specifics required for current authenticated login portal.
			if ($this->_getAppControllerConfig('elements', 'initMenu')) {
				$this->set('menuItems', $this->Menu->getMenu($this->logged['Groups'], Configure::read('Config.language')));
			}
			if (isset($this->{$this->modelClass}->mapping)) {
				$this->set('notificationSystemEnabled', (bool) $this->{$this->modelClass}->mapping['notificationSystem']);
			}
			$this->_setNotifications();
			// $this->_setNews();
		}
		else {
			$this->set('menuItems', []);
		}

		//
		// Set footer data
		$version = Configure::read('Eramba.version');
		$dbVersion = Configure::read('Eramba.Settings.DB_SCHEMA_VERSION');
		$footer = [
			'version' => $version,
			'dbVersion' => $dbVersion,
			'isEnterprise' => strpos($version, 'e') !== false ? true : false
		];
		// 

		$this->set('layout_headerPath', CORE_ELEMENT_PATH . 'header');
		$this->set('layout_toolbarPath', CORE_ELEMENT_PATH . 'toolbar');
		$this->set('layout_pageHeaderPath', CORE_ELEMENT_PATH . 'page_header');
		$this->set('layout_sidebarPath', CORE_ELEMENT_PATH . 'sidebar');
		$this->set('layout_contentPath', false);
		$this->set('layoutFooter', $footer);
	}

	/**
	 * Title and subtitle for a view.
	 * 
	 * @return void
	 */
	protected function _formatTitle()
	{
		$title = empty($this->title) && method_exists($this->_model(), 'label') && !empty($this->_model()->label()) ? $this->_model()->label() : $this->title;
		$description = empty($this->subTitle) && method_exists($this->_model(), 'description') && !empty($this->_model()->description()) ? $this->_model()->description() : $this->subTitle;

		// Defaut config
		$titleDefaults = [
			'value' => $title,
			'ucfirst' => true,
			'period' => true
		];

		// List of titles
		$titles = [
			'title_for_layout' => [
				'value' => $title,
				'period' => false
			],
			'subtitle_for_layout' => [
				'value' => $description
			]
		];
		foreach ($titles as $tag => $options) {
			$options = array_merge($titleDefaults, $options);

			if (!isset($this->viewVars[$tag]) && !empty($options['value'])) {
				$text = $options['value'];

				//
				// Format title
				if ($options['ucfirst'] == true) {
					$text = ucfirst($text);
				} else {
					$text = lcfirst($text);
				}

				if ($options['period'] == true && substr($text, -1) !== '.') {
					$text .= '.';
				} elseif ($options['period'] == false && substr($text, -1) === '.') {
					$text = substr($text, 0, -1);
				}
				//
				
				$this->set($tag, $text);
			}
		}
	}

	/**
	 * Configure new breadcrumbs listed in the layout.
	 */
	protected function _setBreadcrumbs(CakeEvent $event)
	{
		$this->set('useNewBreadcrumbs', true);
		
		$subject = $event->subject;

		//
		// Section
		$sectionLabel = $this->title;
		if (method_exists($subject->model, 'label')) {
			$sectionLabel = $subject->model->label();
		}

		$actualLink = null;
		if (method_exists($subject->model, 'getMappedController')) {
			$actualLink = [
				'controller' => $subject->model->getMappedController(),
				'action' => 'index'
			];
		}
        
        if (!empty($sectionLabel)) {
        	$this->Breadcrumbs->add($sectionLabel, $actualLink);
        }
        //
        
        //
        // Action
        $actionClass = '';
    	$actionLabel = 'Index';
        if ($this->Crud->isActionMapped($this->request->action)) {
        	$actionClass = $subject->crud->action();
        	$actionLabel = $actionClass->mapActionToLabel();
		}

		$this->Breadcrumbs->add($actionLabel);
        //

        // row-level action gets object title
        if (!empty($actionClass) && $actionClass::ACTION_SCOPE === CrudAction::SCOPE_RECORD) {
        	$record = $subject->model->getRecordTitle($subject->request->params['pass']);

            if (!empty($record)) {
            	$this->Breadcrumbs->add($record);
            }
        }
	}

	protected function _setCommonVars(CakeEvent $event)
	{
		$this->set('logged', $this->logged);
		$this->set('currentModel', $this->currentModel);
		$this->set('ldapAuth', $this->ldapAuth);
	}

	protected function _setNotifications()
	{
		// we are disabling notifications element from the template header, for now
		// 
		// $data = $this->Notification->find('all', array(
		// 	'conditions' => array(
		// 		'Notification.user_id' => $this->logged['id'],
		// 		'Notification.status' => 1
		// 	),
		// 	'order' => array('Notification.created' => 'DESC'),
		// 	'recursive' => -1
		// ));

		// $this->set('newNotifications', $data);
	}

	/**
	 * @deprecated
	 */
	protected function _setNews()
	{
		$this->set('shortNews', $this->News->get());
		$this->set('unreadedNewsCount', $this->News->getUnreadedCount());
	}

	/**
	 * Settings for both authentication objects: Form and Basic
	 */
	protected function _setupCommonAuthenticationSettings()
	{
		$this->Auth->authenticate = array(
			AuthComponent::ALL => array(
				'userModel' => 'User',
				'fields' => array(
					'username' => 'login',
					'password' => 'password'
				),
				'passwordHasher' => 'Blowfish'
			)
		);
	}

	/**
	 * For external features with configurations, this allows to do a quick check,
	 * if current user has ACL access to settings section.
	 * 
	 * @return boolean  True with access, False otherwise with redirection as well.
	 */
	protected function checkSettingsAccess()
	{
		$aclCheck = [
			'plugin' => null,
			'controller' => 'settings',
			'action' => 'index'
		];

		if (!$this->AppAcl->check($aclCheck)) {
			$this->Auth->flash($this->Auth->authError);
			$this->Session->write('Auth.redirect', $this->request->here(false));
			$this->redirect($this->Auth->loginAction);
			return false;
		}

		return true;
	}

	/**
	 * Initializes security component with configuration for the whole app.
	 */
	protected function _setupSecurity()
	{
		//using blowfish algoritm
		Security::setHash('blowfish');

		$this->Security = $this->Components->load('Security');

		// default actions used in more than one place
		// @todo remove this and handle in more dry way
		$this->Security->unlockedActions = array(
			'getThreatsVulnerabilities',
			'calculateRiskScoreAjax',
			'auditCalendarFormEntry'
		);

		// for debugging we provide a possibility to toggle/disable security for the entire app.
		if (Configure::read('Eramba.DISABLE_SECURITY')) {
			// we unlock current request
			$this->Security->unlockedActions[] = $this->request->params['action'];
			$this->Security->validatePost = false;
			$this->Security->csrfCheck = false;

			return true;
		}

		$this->Security->unlockedFields = ['modalId', '', 'modalBreadcrumbs'];
		$this->Security->validatePost = false;

		//
		// Disable security for all non-CRUD actions
		$crudActions = ['index', 'add', 'edit', 'delete'];
		$requestAction = $this->request->params['action'];
		if (!in_array($requestAction, $crudActions)) {
			$this->Security->unlockedActions[] = $requestAction;
		}
		//
		
		if ($this->_disableCsrfCheck) {
			$this->Security->csrfCheck = false;
		}

		if ($this->request->is('api')) {
			$this->Security->csrfCheck = false;
		}
	}

	/**
	 * Setup authentication functionality for the app.
	 */
	protected function _setupAuthentication()
	{
		// no flash message error when user is not authorized to access action
		// @todo will be updated during new Ux updates.
		// $this->Auth->authError = __('Your session probably expired and you have been logged out of the application.');
		// $this->Auth->authError .= ' ' .__('It\'s also possible you are not authorized to access this location.');
		$this->Auth->authError = false;
		$this->Auth->flash['element'] = 'error';
		$this->Auth->flash['key'] = 'flash';

		if ($this->isAjax()) {
			// during ajax throw ForbiddenException rather than do redirect when user is unathorized
			$this->Auth->unauthorizedRedirect = false;
		}

		// default login redirect actions
		$this->Auth->loginAction = array('controller' => 'users', 'action' => 'login', 'admin' => false, 'plugin' => null);
		$this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login', 'admin' => false, 'plugin' => null);

		// login redirect action based on user's group
		if (isAdmin($this->Auth->user())) {
			$this->Auth->loginRedirect = [
				'admin' => false,
				'plugin' => 'dashboard',
				'controller' => 'dashboard_kpis',
				'action' => 'admin'
			];
		}
		else {
			$this->Auth->loginRedirect = [
				'admin' => false,
				'plugin' => 'dashboard',
				'controller' => 'dashboard_kpis',
				'action' => 'user'
			];
		}

		// Default scope for logging in
		$scope = array(
			'User.status' => USER_ACTIVE
		);

		//
		// Check if user is Admin
		$isAdmin = false;
		if (isset($this->request->data['User']['login']) && $this->request->data['User']['login'] === 'admin') {
			$isAdmin = true;
		}
		//
		
		$this->ldapAuth = $this->LdapConnectorAuthentication->getAuthData();
		if (isset($this->request->data['User']['login'])) {
			if (!$isAdmin) {
				$authUsers = $this->ldapAuth['LdapConnectorAuthentication']['auth_users'];

				if ($authUsers) {
					$this->_initLdapAuth($this->ldapAuth['AuthUsers'], 'User', 'eramba');
				}
			}
		} elseif (!in_array($this->portal, ['awareness', 'policy'], true)) {
			$oauthGoogle = $this->ldapAuth['LdapConnectorAuthentication']['oauth_google'];
			$authSaml = $this->ldapAuth['LdapConnectorAuthentication']['auth_saml'];
			if ($oauthGoogle) {
				// Only external users can be logged in through OAuth
				$scope['User.local_account'] = 0;

				// Initialize OAuth authentication
				$this->Auth->authenticate['OauthGoogle'] = array(
					'gClient' => $this->OauthGoogleAuth->getGoogleClient(),
					'userModel' => 'User',
					'sessionKey' => $this->OauthGoogleAuth->getSessionKey(),
					'tokenSessionKey' => $this->OauthGoogleAuth->getTokenSessionKey(),
					'fields' => array(
						'username' => 'email'
					),
					'scope' => $scope
				);

				// Set redirect URL
				$this->OauthGoogleAuth->setRedirectUrl($this->Auth->loginAction);
			} elseif ($authSaml) {
				// Only external users can be logged in through SAML
				$scope['User.local_account'] = 0;

				// Initialize Saml authentication
				$this->Auth->authenticate['Saml'] = [
					'AuthObj' => $this->SamlAuth->getAuthObj(),
					'sessionKey' => $this->SamlAuth->getSessionKey(),
					'attributes' => [
						'email' => $this->SamlAuth->getActiveSamlData('email_field')
					],
					'userModel' => 'User',
					'fields' => [
						'username' => 'email'
					],
					'scope' => $scope
				];
			}
		}

		//
		// Setup Form Authentication
		if (!$isAdmin) {
			$scope['User.local_account'] = 1;
		}
		$this->Auth->authenticate['Form'] = array(
			'scope' => $scope
		);
		//
	}

	/**
	 * Authetication and login action for API (have to be called in each controller 
	 * after AppController::beforeFilter() function)
	 */
	protected function _apiAuthInit()
	{
		// Default scope for logging in
		$this->Auth->authenticate['Basic'] = array(
			'scope' => array(
				'User.status' => USER_ACTIVE,
				'User.api_allow' => 1
			)
		);

    	AuthComponent::$sessionKey = false;

    	// Store authorize settings and temporary disable it for login action
		$tempAuthorize = $this->Auth->authorize;
		$this->Auth->authorize = false;
		
		// Identify user
		$this->Auth->startup($this);

		// Load back stored authorize settings
		$this->Auth->authorize = $tempAuthorize;

		// Add groups to user
		if (!empty($this->Auth->user())) {
			$this->Auth->login($this->Auth->user());
		}
	}

	/**
	 * Manages cookies setup with custom options.
	 */
	public function _setupDefaultCookies()
	{
		$this->Cookie->name   = 'ErambaCookie';
		$this->Cookie->time   = '+2 weeks';
		$this->Cookie->domain = $_SERVER['SERVER_NAME'];
		$this->Cookie->key	= 'k886fQz1O787u4r4q07DGvLkjTMP4VZ2pU1wA934Sxsm934mRa';
		
		if (HTTP_PROTOCOL == 'https://') {
			$this->Cookie->secure = true;
		}
		else {
			$this->Cookie->secure = false;
		}

		$this->Cookie->type('cipher');
	}

	/**
	 * After controller setup process.
	 *
	 * @todo  remove this and improve it
	 */
	protected function _afterSetup()
	{
		$this->logged = $this->Auth->user();
		$this->currentModel = $this->modelClass;

		if (!$this->request->is('api'))
		{
			$this->ldapAuth = $this->LdapConnectorAuthentication->getAuthData();
			$this->_afterLoginCheck();
		}

		// for auditable log behavior
		if (!empty($this->request->data) && empty($this->request->data[$this->Auth->userModel])) {
			$user['User']['id'] = $this->Auth->user('id');
			$this->request->data[$this->Auth->userModel] = $user;
		}
	}

	/**
	 * Generic way to retrieve an LdapAuthenticate error message that could happen during user login.
	 * 
	 * @return mixed Message string or False if there are no more details about the error.
	 */
	protected function getLdapLoginError()
	{
		if (!isset($this->Auth->authenticate['LDAP'])) {
			return false;
		}
		
		$userModel = $this->Auth->authenticate['LDAP']['userModel'];
		$this->loadModel($userModel);
		$model = $this->{$userModel};

		if (isset($model->loginErrorMsg) && !empty($model->loginErrorMsg)) {
			return $model->loginErrorMsg;
		}

		return false;
	}

	public function _getLdapLoginError()
	{
		return $this->getLdapLoginError();
	}

	/**
	 * Initialize LDAP Authentication based on connector for any section/controller.
	 */
	protected function _initLdapAuth($connector, $userModel, $loginType)
	{
		$this->Auth->authenticate['LDAP'] = array(
			// the entire connector data
			'LdapConnector' => $connector,

			// other parameters, some of them will be removed in the future
			'ldap_url'		 => $connector['host'],
			'ldap_bind_dn'	 => $connector['ldap_bind_dn'],
			'ldap_bind_pw'	 => $connector['ldap_bind_pw'],
			'ldap_base_dn'	 => $connector['ldap_base_dn'],
			'ldap_filter'	 => $connector['ldap_auth_filter'],
			'ldap_attribute' => $connector['ldap_auth_attribute'],
			'ldap_memberof_attribute' => $connector['ldap_memberof_attribute'],
			'form_fields'	 => array('username' => 'login', 'password' => 'password'),
			'userModel' 	 => $userModel,
			'loginType' => $loginType
		);
	}

	public function beforeRender()
	{
		// Compatibility after removing mappingcomponent

		/**
		 * If child controller doesn't have CRUD or called action was not mapped in CRUD, 
		 * trigger content from AppController's beforeRender callback manually
		 * 
		 * Note 1: We can't trigger original CRUD's beforeRender callback because 
		 * there is possibility that child controller's CRUD doesn't have mapped 
		 * a called action so other listeners could fail because of "Action is not mapped" 
		 * exception or unwanted beforeRender logic from child controller could be executed
		 * 
		 * Note 2: This is only for compatibility reasons with non-CRUD child controllers or non-CRUD actions
		 */
		if (!$this->Crud->isActionMapped($this->request->action)) {
			$triggerEvent = true;
			foreach ($this->Crud->eventLog() as $eventLog) {
				if (in_array($this->Crud->settings['eventPrefix'] . '.beforeRender', $eventLog)) {
					$triggerEvent = false;
				}
			}
			if ($triggerEvent) {
				$this->Crud->on('beforeRenderCompatibility', function(CakeEvent $event)
				{
					$this->_beforeRenderHandler($event);
				});
				$this->Crud->trigger('beforeRenderCompatibility');
			}
		}

		// disable AclCheck if acl authorize is disabled
		if ($this->Auth->authorize === false) {
			AclCheck::disable();
		}
	}

	// compatibility after removing mappingcomponent
	// @deprecated
	protected function handleSystemRecords($cron = false)
	{
		if ($this->recordsHandled) {
			return true;
		}

		$ret = true;

		App::uses('SystemLogBehavior', 'Model/Behavior');
		SystemLogBehavior::$isCron = $cron;
		foreach (SystemLogBehavior::$Models as $model) {
			$ret &= $model->handleSystemRecords();
		}
		SystemLogBehavior::$isCron = true;

		if ($ret) {
			$this->recordsHandled = true;
		}

		return $ret;
	}

	protected function forceSSL()
	{
		$this->redirect('https://' . env('SERVER_NAME') . $this->here);
		exit;
	}

	/**
	 * @deprecated Moved to bootstrap functions.
	 */
	protected function isSSL()
	{
		return isSSL();
	}

	/**
	 * Certain actions taking place right after a successful login. System health checking for example.
	 */
	protected function _afterLoginCheck()
	{
		if ($this->Session->check('UserLogged') && $this->Session->read('UserLogged')) {
			$this->Session->delete('UserLogged');

			$SystemHealthLib = new SystemHealthLib();
			$systemHealthData = $SystemHealthLib->checkCriticalStatuses();

			$AutoUpdateLib = new AutoUpdateLib();
			
			$this->set('userJustLogged', true);
			$this->set('systemHealthData', $systemHealthData);
			$this->set('autoUpdatePending', $AutoUpdateLib->hasPending());
		}
		else {
			$this->set('userJustLogged', false);
		}
	}

	protected function getPageLimit()
	{
		//if the limit was changed
		if (isset($this->request->params['named']['limit']) && is_numeric($this->request->params['named']['limit'])) {
			$limit = $this->request->params['named']['limit'];
			Cache::write('page_limit_' . $this->logged['id'], $limit, 'infinite');
		}
		//if the cache is invalid
		elseif (($limit = Cache::read('page_limit_' . $this->logged['id'], 'infinite')) === false) {
			$limit = DEFAULT_PAGE_LIMIT;
		}

		$this->set('currPageLimit', $limit);

		return $limit;
	}

	/**
	 * Returns array of users with full names used in select inputs.
	 * @return array User list.
	 * @todo  move this away from appcontroller.
	 */
	protected function getUsersList($includeLogged = true)
	{
		$conds = array();
		if (!$includeLogged) {
			$conds['User.id !='] = $this->logged['id'];
		}

		$this->loadModel('User');
		$users_all = $this->User->find('all', array(
			'conditions' => $conds,
			'order' => array('User.name' => 'ASC'),
			'fields' => array('User.id', 'User.name', 'User.surname'),
			'recursive' => -1
		));

		$users = array();
		foreach ( $users_all as $user ) {
			$users[ $user['User']['id'] ] = $user['User']['name'] . ' ' . $user['User']['surname'];
		}

		return $users;
	}

	/**
	 * Returns search query in url.
	 */
	protected function getSearchQuery()
	{
		if ( isset( $this->request->query['search'] ) && $this->request->query['search'] != '' ) {
			$keyword = '%' . $this->request->query['search'] . '%';
			return $keyword;
		}

		return false;
	}

	/**
	 * Returns array of only Released security policies.
	 * @return array Security policy list.
	 * 
	 * @deprecated New Template 	Everything moved to FieldData
	 */
	protected function getSecurityPoliciesList()
	{
		$this->loadModel('SecurityPolicy');

		$data = $this->SecurityPolicy->getListWithType(['SecurityPolicy.status' => SECURITY_POLICY_RELEASED]);

		return $data;
	}

	/**
	 * @todo  move from appcontroller
	 */
	protected function getDayDiffFromToday( $date = false )
	{
		if ( ! $date ) {
			return false;
		}

		$today = CakeTime::format( 'Y-m-d', CakeTime::fromString( 'now' ) );
		$datetime1 = new DateTime( $today );
		$datetime2 = new DateTime( $date );
		$interval = $datetime1->diff( $datetime2 );
		$diff = $interval->format( '%a' );

		return $diff;
	}

	/**
	 * Convert timestamp to javascript default date format - new Date().
	 * @todo  move away from appcontroller
	 */
	protected function toJsDateFormat( $timestamp = null )
	{
		if ( ! $timestamp ) {
			$timestamp = CakeTime::fromString( 'now' );
		}

		return CakeTime::format('Y-m-d', $timestamp);
		// return CakeTime::format( 'D M d Y', $timestamp );
	}

	/**
	 * Checks if date is expired.
	 * @todo  move from here
	 */
	protected function isExpired( $date = null, $status = null )
	{
		$today = CakeTime::format( 'Y-m-d', CakeTime::fromString( 'now' ) );
		if ( $status !== null ) {
			if ( $date < $today && $status == 1 ) {
				return true;
			}
		} else {
			if ( $date < $today ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Calculate residual risk.
	 * @param  int $residual_score Residual Score.
	 * @param  int $risk_score     Risk Score.
	 * @return int                 Residual Risk.
	 * @todo  move away from appcontroller
	 */
	protected function getResidualRisk($residual_score, $risk_score)
	{
		return CakeNumber::precision(getResidualRisk($residual_score, $risk_score), 2);
	}

	/**
	 * Public alias of AppController::getIndexUrl(), to allow access from components.
	 */
	public function _getIndexUrl($model, $id = null, $action = 'index') {
		return $this->getIndexUrl($model, $id, $action);
	}

	/**
	 * Get index/custom page array url based on model, id and model settings.
	 *
	 * @param  string $model  Model name of the item.
	 * @param  int    $id     ID of the item.
	 * @param  string $action Use custom action page, otherwise list items on index based on Model's mapping settings.
	 * @return array          Redirect URL.
	 * @todo  remove
	 */
	protected function getIndexUrl($model, $id = null, $action = 'index')
	{
		$this->loadModel($model);
		if ($action == 'index') {
			if (!$this->{$model}->mapping['indexController']) {
				$controller = Inflector::variable(Inflector::tableize($model));
				$url = array('controller' => $controller, 'action' => 'index');
			}
			else {
				$map = $this->{$model}->mapping['indexController'];

				// if model has an array of custom settings for this mapping and ID provided.
				if (is_array($map) && $id) {
					$contain = false;
					if (isset($map['crawl'])) {
						$contain = $map['crawl'];
					}

					if ($this->{$model}->mapping['workflow'] !== false) {
						$this->{$model}->alterQueries(true);
					}

					$data = $this->{$model}->find('first', array(
						'conditions' => array(
							$model . '.id' => $id
						),
						'contain' => $contain
					));

					if ($this->{$model}->mapping['workflow'] !== false) {
						$this->{$model}->alterQueries();
					}

					$extraParams = array();
					if (!empty($map['params'])) {
						// parse params required for the Url based on model mapping.
						$extraParams = $this->getParams($data, $map['params']);
					}

					if ($action == 'index' && isset($map['action'])) {
						$action = $map['action'];
					}

					$urlBase = array('controller' => $map['advanced'], 'action' => $action);
					$url = am($urlBase, $extraParams);
				}
				elseif (is_array($map) && !$id) {
					$url = array('controller' => $map['basic'], 'action' => 'index');
				}
				else {
					$url = array('controller' => $map, 'action' => 'index', $id);
				}
			}
		}
		else {
			$map = $this->{$model}->mapping['indexController'];

			if (!$map) {
				$controller = Inflector::variable(Inflector::tableize($model));
				$url = array('controller' => $controller, 'action' => $action, $id);
			}
			else {
				if (is_array($map)) {
					if (isset($map['action'])) {
						$action = $map['action'];
					}

					$url = array('controller' => $map['advanced'], 'action' => $action, $id);
				}
				else {
					$url = array('controller' => $map, 'action' => $action, $id);
				}
			}
		}

		return $url;
	}

	private function getParams($arr, $params)
	{
		$values = array();
		foreach ($params as $param) {
			$values = $this->getParamsFinder($arr, $param, $values);
		}

		return $values;
	}

	private function getParamsFinder($arr, $param, $values)
	{
		$keys = array_keys($arr);
		if (in_array($param, $keys, true)) {
			$values[] = $arr[$param];
		}
		foreach ($keys as $key) {
			if (is_array($arr[$key])) {
				$values = $this->getParamsFinder($arr[$key], $param, $values);
			}
		}

		return $values;
	}

	/**
	 * Sends user to home after hitting unavailable request.
	 */
	protected function actionUnavailable($url = array('controller' => 'pages', 'action' => 'welcome'))
	{
		$this->Session->setFlash(__('Required action is not available.'), FLASH_ERROR);
		$this->redirect($url);
		exit;
	}

	protected function getScopesOptions()
	{
		$this->loadModel('Scope');
		$scope = $this->Scope->find('first', array(
			'recursive' => -1
		));

		$scopes = array();
		if (!empty($scope)) {
			if (!empty($scope['Scope']['ciso_role_id'])) {
				$scopes['ciso_role'] = __('CISO Role');
			}
			if (!empty($scope['Scope']['ciso_deputy_id'])) {
				$scopes['ciso_deputy'] = __('CISO Deputy');
			}
			if (!empty($scope['Scope']['board_representative_id'])) {
				$scopes['board_representative'] = __('Board Representative');
			}
			if (!empty($scope['Scope']['board_representative_deputy_id'])) {
				$scopes['board_representative_deputy'] = __('Board Representative Deputy');
			}
		}

		return $scopes;
	}

	/**
	 * Allows only ajax requests, otherwise it will exit the function.
	 */
	protected function allowOnlyAjax()
	{
		if (!$this->request->is('ajax')) {
			exit;
		}
	}

	protected function isAjax()
	{
		return $this->request->is('ajax');
	}

	public function downloadAttachment($id)
	{
		// ensure component
		if (!$this->Components->enabled('Attachments.AttachmentsMgt')) {
			$AttachmentsMgt = $this->Components->load('Attachments.AttachmentsMgt');
			$AttachmentsMgt->startup($this);
		}

		if (!$AttachmentsMgt->isAllowedToDownload($id)) {
			throw new ForbiddenException(__('You are not allowed to download this attachment.'));
		}

		return $AttachmentsMgt->download($id);
	}

	/**
	 * Generic method checks user Auth session if current account requires first login account configuration
	 * in order to use eramba.
	 */
	protected function _checkAccountReady()
	{
		$loggedUser = $this->Auth->user();
		$params = $this->request->params;

		if (empty($loggedUser) ||
			($params['controller'] == 'cron' &&
			($params['action'] == 'job' || $params['action'] == 'task'))) {
			return true;
		}

		// account_ready column is available for auth keys below
		$checkForAuths = [
			'Auth.User',
			'Auth.AccountReview',
			'Auth.VendorAssessment'
		];

		$triggerConds = in_array(AuthComponent::$sessionKey, $checkForAuths);
		$triggerConds &= isset($loggedUser['account_ready']) && $loggedUser['account_ready'] == 0;
		
		if ($triggerConds &&
			$params['controller'] !== 'users') {
			$this->redirect(['plugin' => false, 'admin' => false, 'controller' => 'users', 'action' => 'prepareAccount', $loggedUser['id'], '?' => [
				'redirect' => Router::url(null, true)
			]]);
		}
	}

	/**
	 * Check if user changed his defualt password and if no, redirect him to the change password form
	 * @return boolean [description]
	 */
	protected function _checkDefaultPasswordChange()
	{
		$loggedUser = $this->Auth->user();
		$params = $this->request->params;
		if (empty($loggedUser) ||
			($params['controller'] == 'cron' &&
			($params['action'] == 'job' || $params['action'] == 'task'))) {
			return true;
		}

		if ($this->Auth->isPasswordChangeRequired($loggedUser['id']) &&
			$this->request->params['controller'] !== 'users') {
			if ($this->request->is('api')) {
				throw new ForbiddenException(__('User who is trying to login uses default password. To be able to use this account, change default password first.'));
			} else {
				$this->redirect(['plugin' => false, 'admin' => false, 'controller' => 'users', 'action' => 'changeDefaultPassword', $loggedUser['id'], '?' => [
					'redirect' => Router::url(null, true)
				]]);
			}
		}
	}

	protected function _checkAssessmentPermissions($model = null, $foreignKey = null, $user = null, $strict = false)
	{
		if (!AppModule::loaded('VendorAssessments')) {
			return;
		}

		App::uses('VendorAssessmentsModule', 'VendorAssessments.Lib');

		if ($model === null && !empty($this->request->params['pass'][0])) {
			$model = $this->request->params['pass'][0];
		}

		if ($foreignKey === null && !empty($this->request->params['pass'][1])) {
			$foreignKey = $this->request->params['pass'][1];
		}

		if ($user === null) {
			$user = $this->logged;
		}

		if (($user = $this->Session->read(VendorAssessmentsModule::getSessionKey())) !== null 
			&& !empty($model)
			&& !empty($foreignKey)
			&& in_array($model, ['VendorAssessments.VendorAssessmentFeedback', 'VendorAssessments.VendorAssessmentFinding', 'VendorAssessments.VendorAssessment'])
			&& !ClassRegistry::init($model)->userHasPermission($foreignKey, $user, $strict)
		) {
			throw new NotFoundException();
		}
	}
}
