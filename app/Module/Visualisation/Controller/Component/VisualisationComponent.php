<?php

App::uses('Component', 'Controller');
App::uses('Group', 'Model');

/**
 * Visualisation for CRUD base on objects permission.
 */
class VisualisationComponent extends Component {
	public $components = ['Session', 'Auth', 'Crud', 'Acl', 'CustomRoles.CustomRoles'];
	public $settings = [
		'listenerClass' => 'Visualisation.Visualisation'
	];

	// temporary solution to store user's groups within a request
	protected $_usersGroupsCache = [];

	/**
	 * Runtime configuration values.
	 * 
	 * @var array
	 */
	protected $_runtime = [
	];

	/**
	 * Reference to the current event manager.
	 *
	 * @var CakeEventManager
	 */
	protected $_eventManager;

	public function __construct(ComponentCollection $collection, $settings = array()) {
		if (empty($this->settings)) {
			$this->settings = array(
			);
		}

		$settings = array_merge($this->settings, (array)$settings);
		parent::__construct($collection, $settings);

		$this->_runtime = $this->settings;
	}

	public function model() {
		return $this->Crud->getSubject()->modelClass;
	}

	// is visualisation enabled in user configurations
	public function isEnabled() {
		return $this->_runtime['enabled'];
	}

	// get the current active listener class
	public function listener() {
		return $this->_runtime['listener'];
	}

	protected function _setAclAdapter() {
		// load up different adapter for the ACL component
		list($plugin, $name) = pluginSplit(Configure::read('Visualisation.Acl.classname'));
		$this->Acl->adapter($name);
	}

	public function cleanup() {
		$name = Configure::read('Acl.classname');
		list($plugin, $name) = pluginSplit($name, true);
		$this->Acl->adapter($name);
	}

	public function initialize(Controller $controller) {
		$this->controller = $controller;
		$this->_eventManager = $this->controller->getEventManager();

		$SettingClass = ClassRegistry::init('Visualisation.VisualisationSetting');
		$this->_runtime['enabled'] = $SettingClass->isEnabled($this->model());

		// if enabled we load up the listener class to handle required events
		if ($this->isEnabled()) {
			

			$model = $this->Crud->getSubject()->model;

			// we attach necessary handlers for a permission check
			// that includes VisualisationUser model's check and CustomRole's model check
			$this->on('onCheck', array($this, 'onCheck'), ['passParams' => true]);

			// we only incorporate a CustomRoles::onCheck() listener,
			// in case CustomRolesBehavior loaded on the model
			if ($model->Behaviors->enabled('CustomRoles') || $model instanceof InheritanceInterface) {
				// make sure CustomRoles component is loaded for the 'onCheck' callback to be available
				$this->_Collection->load('CustomRoles.CustomRoles');

				// and also initialized with required class variables
				$this->CustomRoles->initialize($controller);

				// finally attach a listener for ACL custom roles permission check
				$this->on('onCheck', array($this->CustomRoles, 'onCheck'), ['passParams' => true]);
			}

			// let the VisualisationListener handle index pagination and items that are pulled from database
			// by the user's permission
			$this->Crud->addListener('Visualisation', $this->settings['listenerClass']);
			$this->_runtime['listener'] = $this->Crud->listener('Visualisation');

			$this->controller->set('visualisationEnabled', true);
		}
		else {
			$this->Crud->removeListener('Visualisation');
			$this->controller->set('visualisationEnabled', false);
		}

		$this->controller->set('visualisationModel', $this->model());
	}

	/**
	 * Before render callback used to set vars for Visualisation module.
	 */
	public function beforeRender(Controller $controller) {
		// $this->_setSharedData($controller);
	}

	/**
	 * Set visualisation data for already-shared objects for the View.
	 */
	protected function _setSharedData(Controller $controller) {
		$VisualisationShare = ClassRegistry::init('Visualisation.VisualisationShare');
		$sectionData = $VisualisationShare->listAll($this->model());

		$controller->set('visualisationSectionShared', $sectionData);
	}

	/**
	 * This is a wrapper method that dispatches a check event to validate $requestor access to an $object.
	 * 
	 * @param  int    		$requestor 	Requestor, which is a user ID.
	 * @param  Model    	$model 		Model instance.
	 * @param  int|array  	$object 	Controlled object(s), either integer as ID which should return boolean value or
	 *                              	array of IDs in which case it should return array of allowed IDs
	 *                               
	 * @param  string 		$action    	Type of access to check - read, create, update or delete.
	 * @return boolean                  True if $requestor has $action access to single $object or array of IDs if multiple
	 *                                  $objects provided in array.
	 */
	public function check($requestor, Model $model, $object, $action) {
		if (!$this->Acl->adapter() instanceof VisualisationDbAcl) {
			$this->_setAclAdapter();
		}

		$event = new CakeEvent('Visualisation.onCheck', $this, array($requestor, $model, $object, $action));
		list($event->break, $event->breakOn) = array(true, true);
		$this->_eventManager->dispatch($event);
		if ($event->result === true) {
			return true;
		}

		return false;
	}

	/**
	 * Validates VisualisationUser access to $object.
	 * Returns True or nothing which is NULL, because False would stop event dispatcher.
	 */
	public function onCheck($requestor, Model $model, $object, $action) {
		// App::uses('VisualisationUser', 'Visualisation.Model');
		$rUser = [
			'Visualisation.VisualisationUser' => [
				'id' => $requestor
			]
		];

		// find groups
		if (!isset($this->_usersGroupsCache[$requestor])) {
			$userGroups = ClassRegistry::init('UsersGroup')->find('list', [
				'conditions' => [
					'UsersGroup.user_id' => $requestor
				],
				'fields' => [
					'UsersGroup.group_id'
				],
				'recursive' => -1
			]);

			$this->_usersGroupsCache[$requestor] = $userGroups;
		} else {
			$userGroups = $this->_usersGroupsCache[$requestor];
		}

		$rGroup = [
			'Visualisation.VisualisationGroup' => [
				'id' => $userGroups
			]
		];

		$ret = $this->visualisationAcl($rUser, $model, $object, $action);
		$ret = $ret || $this->visualisationAcl($rGroup, $model, $object, $action);
		if ($ret) {
			return true;
		}

		return null;
	}

	public function visualisationAcl($requestorNode, Model $model, $object, $action) {
		$ret = false;

		$section = $model;
		$id = $object;

		if ($section->alias == 'AppNotification') {
			$notification = ClassRegistry::init('AppNotification.AppNotification')->find('first', [
				'conditions' => [
					'id' => $id
				],
				'recursive' => -1
			]);

			if ($notification['AppNotification']['model'] == 'News.News') {
				return true;
			}
			
			$section = ClassRegistry::init($notification['AppNotification']['model']);
			$id = $notification['AppNotification']['foreign_key'];
		}

		if ($section->alias == 'DashboardCalendarEvent') {
			$event = ClassRegistry::init('Dashboard.DashboardCalendarEvent')->find('first', [
				'conditions' => [
					'id' => $id
				],
				'recursive' => -1
			]);

			$section = ClassRegistry::init($event['DashboardCalendarEvent']['model']);
			$id = $event['DashboardCalendarEvent']['foreign_key'];
		}

		$sectionNode = [
			$section->alias => [
				$section->primaryKey => ''
			]
		];

		// first check section node tree
		$ret = $ret || $this->Acl->check($requestorNode, $sectionNode, $action);

		// then object node tree if possible
		if (!empty($id)) {
			$objectNode = [
				$section->alias => [
					$section->primaryKey => $id
				]
			];

			$ret = $ret || $this->Acl->check($requestorNode, $objectNode, $action);
		}


		return $ret;
	}

	// defined a new events for this class
	public function implementedEvents() {
		return parent::implementedEvents() + [
			'Visualisation.onCheck' => array('callable' => 'onCheck', 'passParams' => true)
		];
	}

	/**
	 * Normalizes node array for ACL compatible use.
	 * 
	 * @param  mixed $node  Node.
	 * @return Normalized array node.
	 */
	public function normalize($node) {
		if (is_array($node)) {
			$node = array_values($node);
			$primary = 'id';

			if (!is_object($node[0])) {
				$node[0] = ClassRegistry::init($node[0]);
			}

			$node[0]->id = $node[1];
			return $node[0];
		}

		return $node;
	}

	/**
	 * Attaches an event listener function to the controller.
	 */
	public function on($events, $callback, $options = array()) {
		foreach ((array)$events as $event) {
			if (!strpos($event, '.')) {
				$event = 'Visualisation' . '.' . $event;
			}

			$this->_eventManager->attach($callback, $event, $options);
		}
	}

	public function integrityCheck() {
	}

	public function syncObjects() {
	}
}
