<?php
App::uses('Review', 'Model');
class PolicyController extends AppController {
	public $helpers = array('Html', 'Form', 'UserFields.UserField', 'Translations.Translations');
	public $components = [
		'Session', 'Pdf', 'Search.Prg' => array('presetForm' => array('model' => 'SecurityPolicy')), 'Attachments.AttachmentsMgt', 'UserFields.UserFields'
	];
	public $uses = array('Policy', 'SecurityPolicy', 'Attachments.Attachment');
	private $allowedForLogged = false;

	protected $_appControllerConfig = [
		'components' => [
			'AppNotification.AppNotifications' => false
		],
		'helpers' => [
		],
		'elements' => [
			'initMenu' => false
		]
	];

	public function beforeFilter()
	{
		$this->portal = 'policy';

		$query = $this->request->query;
		$auth = $this->Session->read('Auth');

		//@todo remove this, too chaotic
		if (/*isset($query['allowForLogged']) && $query['allowForLogged'] && */isset($auth['User']) && !empty($auth['User'])) {
			$this->Auth->allow('document', 'documentPdf', 'downloadAttachment');
			$this->set('allowedForLogged', true);
			$this->allowedForLogged = true;
		}

		parent::beforeFilter();
		$this->layout = 'policy';

		$this->set('isGuest', $this->isGuest());
	}

	/**
	 * Extended authentication only applies to policy portal.
	 */
	protected function _setupAuthentication() {
		parent::_setupAuthentication();

		// policy portal doesnt require ACL
		$this->Auth->authorize = false;
		$this->Auth->authError = false;
		$this->Auth->allow('guestLogin', 'documentDirect');

		AuthComponent::$sessionKey = 'Auth.Policy';
		$this->Auth->loginAction = array('controller' => 'policy', 'action' => 'login', 'admin' => false, 'plugin' => null);
		$this->Auth->loginRedirect = array('controller' => 'policy', 'action' => 'index', 'admin' => false, 'plugin' => null);
		$this->Auth->logoutRedirect = array('controller' => 'policy', 'action' => 'login', 'admin' => false, 'plugin' => null);

		$ldapAuth = $this->LdapConnectorAuthentication->getAuthData();
		$ldapConfig = $ldapAuth['LdapConnectorAuthentication'];
		if ($ldapConfig['auth_policies'] && !empty($ldapConfig['auth_policies_id'])) {
			$this->_initLdapAuth($ldapAuth['AuthPolicies'], 'PolicyUser', 'policy');
		}
	}

	/**
	 * Skipping.
	 */
	protected function _currentAuthExtras() {
		// already enough
	}

	/**
	 * Policy portal login.
	 */
	public function login() {
		
		//
		// If portal is disabled
		if (empty($this->ldapAuth['LdapConnectorAuthentication']['auth_policies'])) {
			echo __('Policy portal is disabled. Please go to Eramba -> Settings -> Authentication to enable it.');
			exit;
		}
		//

		if (!empty($this->ldapAuth['LdapConnectorAuthentication']['auth_policies']) && empty($this->ldapAuth['LdapConnectorAuthentication']['auth_policies_id'])) {
			$this->redirect(array('action' => 'guestLogin'));
		}

		$this->layout = 'login';
		$this->set('title_for_layout', __('Policy Portal Login'));

		$this->set('hasPublicDocs', $this->SecurityPolicy->hasPublicDocuments());

		if ($this->logged != null) {
			$this->redirect($this->Auth->loginRedirect);
		}

		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$userId = $this->Auth->user('id');
				return $this->redirect($this->Auth->redirect());
			}
			else {
				$errorMsg = __('Email or password was incorrect.');

				$ldapErr = $this->getLdapLoginError();
				if (!empty($ldapErr)) {
					$errorMsg = $ldapErr;
				}

				$this->Session->setFlash($errorMsg, FLASH_ERROR);
			}
		}

		$this->Translations->setAvailableTranslations();

		$this->set('loginTitle', __('Document Library Portal'));
		$this->set('model', 'PolicyUser');
		$this->set('loginFormPath', 'policy/login_form');
	}

	/**
	 * Login as guest to see only public documents.
	 */
	public function guestLogin() {
		if (!$this->SecurityPolicy->hasPublicDocuments()) {
			$this->Session->setFlash(__('There are no public documents available.'), FLASH_ERROR);
			// return $this->redirect($this->Auth->logout());
		}

		$guestLogin = array(
			'id' => null,
			'login' => 'guest',
			'name' => __('Guest'),
			'surname' => false
		);

		if ($this->Auth->login($guestLogin)) {
			// $this->Flash->set(__('You have been auto logged in as Guest user.'));
			return $this->redirect($this->Auth->redirect());
		}
		else {
			$this->Session->setFlash(__('Could not login as guest. Try again please.'), FLASH_ERROR);
			return $this->redirect($this->Auth->logout());
		}
	}

	/**
	 * Logout function.
	 */
	public function logout() {
		$this->redirect($this->Auth->logout());
	}

	/**
	 * Index for document listing.
	 */
	public function index($policyId = null) {
		$this->presetVars = array(
			array('field' => 'policy_search', 'type' => 'value')
		);

		$this->SecurityPolicy->filterArgs['policy_search'] = array(
			'type' => 'query',
	       	'method' => 'searchPolicyCondition'
		);

		$this->set('title_for_layout', __('Policy Portal'));

		$this->Prg->commonProcess('SecurityPolicy');
		$filterConditions = $this->SecurityPolicy->parseCriteria($this->Prg->parsedParams());

		$conds = array(
			'SecurityPolicy.status' => 1,
			'SecurityPolicy.permission !=' => SECURITY_POLICY_PRIVATE
		);

		// if guest show only public documents
		if ($this->isGuest()) {
			$conds['SecurityPolicy.permission'] = SECURITY_POLICY_PUBLIC;
		}

		$documents = $this->SecurityPolicy->find('all', array(
			'conditions' => am($conds, $filterConditions),
			'fields' => array(
				'SecurityPolicy.id',
				'SecurityPolicy.index',
				'SecurityPolicy.short_description',
				'SecurityPolicy.security_policy_document_type_id',
				'SecurityPolicy.permission',
			),
			'contain' => array(
				'SecurityPolicyLdapGroup' => array(
					'fields' => array('name')
				),
				'SecurityPolicyDocumentType' => [
					'fields' => ['id', 'name']
				]
			),
			'order' => array('SecurityPolicy.index' => 'ASC'),
			'recursive' => 2
		));
		$documents = $this->filterData($documents);

		//get all not deleted policy IDs for tags
		$policyIds = $this->SecurityPolicy->find('list', ['fields' => ['SecurityPolicy.id']]);

		$this->set('documents', $documents);
		$this->set('tags', $this->SecurityPolicy->Tag->getTags('SecurityPolicy', $policyIds));
		$this->set('policyId', $policyId);
	}

	private function filterData($data) {
		foreach ($data as $key => $item) {
			if ($item['SecurityPolicy']['permission'] == SECURITY_POLICY_PUBLIC) {
				continue;
			}

			if (empty($this->logged['ldapGroup']) || empty($item['SecurityPolicyLdapGroup'])) {
				unset($data[$key]);
				continue;
			}

			$hasPermission = false;
			foreach ($item['SecurityPolicyLdapGroup'] as $group) {
				if (in_array($group['name'], $this->logged['ldapGroup'])) {
					$hasPermission = true;
					break;
				}
			}

			if (!$hasPermission) {
				unset($data[$key]);
			}
		}

		return $data;
	}

	/**
	 * Checks if user logged is guest.
	 */
	public function isGuest() {
		if (empty($this->logged)) {
			return false;
		}

		return $this->logged['login'] == 'guest';
	}

	public function appDocument($id)
	{
		$this->document($id);
	}

	/**
	 * Shows a document details.
	 */
	public function document($id) {
		$this->allowOnlyAjax();

		$conds = array(
			'SecurityPolicy.id' => $id,
			'SecurityPolicy.status' => 1,
		);

		if ($this->allowedForLogged === false) {
			$conds['SecurityPolicy.permission !='] = SECURITY_POLICY_PRIVATE;
		}

		// if guest show only public documents
		if ($this->isGuest()) {
			$conds['SecurityPolicy.permission'] = SECURITY_POLICY_PUBLIC;
		}

		$document = $this->setDocument($conds, !$this->allowedForLogged);

		if (empty($this->request->query['from_app'])) {
			$this->YoonityJSConnector->deny();
		}
		else {
			$this->Modals->init();
			if (isset($document['SecurityPolicy']['index'])) {
				$this->Modals->setHeaderHeading($document['SecurityPolicy']['index']);
			}

			$this->Modals->addFooterButton(__('Download PDF'), [
				'class' => 'btn btn-primary',
				'href' => Router::url(['admin' => false, 'plugin' => false, 'controller' => 'policy', 'action' => 'documentPdf', $id]),
			], 'downloadBtn');

			$this->Modals->changeConfig('footer.buttons.downloadBtn.tag', 'a');

			$this->render('document_wrapped');
		}
	}

	private function setDocument($conds = array(), $checkLdap = true) {
		$this->SecurityPolicy->bindAttachments();
        $this->SecurityPolicy->Review->bindAttachments();

        $contain = array(
			'LogSecurityPolicy' => array(
				'fields' => array('id', 'index', 'short_description', 'version', 'user_edit_id'),
				'order' => array('created' => 'DESC'),
				'UserEdit'
			),
			'SecurityPolicyDocumentType',
			'RelatedDocuments' => $this->getRelatedDocsParams(),
			'Attachment',
			'Review' => array(
				'Attachment' => array(
					'conditions' => array('model' => array('SecurityPolicyReview', 'Review'))
				)
			),
			'SecurityPolicyLdapGroup'
		);

		if (AppModule::loaded('CustomFields')) {
			$contain[] = 'CustomFieldValue';
		}

		$document = $this->SecurityPolicy->find('first', array(
			'conditions' => $conds,
			'contain' => $this->UserFields->attachFieldsToArray(['Owner', 'Collaborator'], $contain, 'SecurityPolicy')
		));

		$id = $conds['SecurityPolicy.id'];
		
		if (empty($document) || ($checkLdap && !$this->checkLdapGroupAccess($document['SecurityPolicy']['id']))) {
			$this->set('documentNotAvailable', true);
			// throw new NotFoundException();
		} else {
			$currentReview = ClassRegistry::init('SecurityPolicyReview')->getCurrentReview($id);
			$document['CurrentReview'] = $currentReview;
		}

		$reviews = $this->SecurityPolicy->getPolicyReviews($id);
		$document['ReviewVersion'] = Hash::extract($reviews, '{n}.Review');
		// debug($document);

		// $this->CustomFieldsMgt = $this->Components->load('CustomFields.CustomFieldsMgt', [
		// 	'model' => 'SecurityPolicy'
		// ]);
		// $this->CustomFieldsMgt->initialize($this);
		// $customFieldsData = $this->CustomFieldsMgt->setData();
		$customFieldsData = [];
		$this->set('document', array_merge($document, $customFieldsData));

		return $document;
	}

	private function checkLdapGroupAccess($documentId) {
		$document = $this->SecurityPolicy->find('first', array(
			'conditions' => array(
				'SecurityPolicy.id' => $documentId
			),
			'fields' => array(
				'SecurityPolicy.id',
				'SecurityPolicy.permission'
			),
			'contain' => array(
				'SecurityPolicyLdapGroup'
			)
		));

		if ($document['SecurityPolicy']['permission'] == SECURITY_POLICY_LOGGED) {
			$accessGroups = array();
			foreach ($document['SecurityPolicyLdapGroup'] as $group) {
				$accessGroups[] = $group['name'];
			}

			if (empty($this->logged['ldapGroup'])) {
				return false;
			}

			if (array_intersect($accessGroups, $this->logged['ldapGroup'])) {
				return true;
			}
			else {
				return false;
			}
		}

		return true;
	}

	private function getRelatedDocsParams() {
		$relatedDocsParams = array(
			'conditions' => array(
				'status' => 1
			),
			'fields' => array('id', 'index', 'security_policy_document_type_id'),
			'order' => array('index' => 'ASC'),
			'SecurityPolicyDocumentType'
		);

		if ($this->isGuest()) {
			$relatedDocsParams['conditions']['permission'] = SECURITY_POLICY_PUBLIC;
		}

		return $relatedDocsParams;
	}

	/**
	 * Direct url for a document.
	 */
	public function documentDirect($hash = null) {
		$this->layout = 'policy-external';

		$data = $this->SecurityPolicy->find('first', array(
			'conditions' => array(
				'SecurityPolicy.hash' => $hash
			),
			'fields' => array('id', 'index'),
			'recursive' => -1
		));

		if (empty($data)) {
			$this->set('documentNotAvailable', true);
			return true;
			//throw new NotFoundException();
		}

		$conds = $this->getConditions($data);

		$this->setDocument($conds, !$this->allowedForLogged);
		$this->set('externalDocument', true);

		// temporary solution - documentNotAvailable configured in setDocument() method
		if (empty($this->viewVars['documentNotAvailable'])) {
			$this->set('title_for_layout', sprintf(__('Policy - %s', $data['SecurityPolicy']['index'])));
		} else {
			$this->set('title_for_layout', sprintf(__('Policy - Not Available Document')));
		}
	}

	/**
	 * PDF function.
	 */
	public function documentPdf($id) {
		$this->autoRender = false;

		$conds = $this->getConditions(array('SecurityPolicy' => array('id' => $id)));
		unset($conds['SecurityPolicy.permission !=']);
		$document = $this->setDocument($conds, !$this->allowedForLogged);
		$vars = array(
			'document' => $document,
			'externalDocument' => true,
			'pdfDocument' => true
		);

		$auth = $this->Session->read('Auth');
		
		$allowDownloadConds = !empty($document);
		$allowDownloadConds &= !empty($auth['User']) || !empty($auth['Policy']);
		if ($allowDownloadConds) {
			$name = Inflector::slug($document['SecurityPolicy']['index'], '-');
			$this->Pdf->renderPdf($name, '..'.DS.'Policy'.DS.'document_direct', 'policy-external', $vars, true);
		} else {
			$this->Session->setFlash( __('You don\'t have a permission to view this document.'), FLASH_ERROR );
			$this->redirect( array( 'controller' => 'securityPolicies', 'action' => 'index' ) );
		}
	}

	/**
	 * Let a policy logged in user download an attachment which belongs to Policies that have a document type set as Use Attachments
	 */
	public function downloadAttachment($id) {
		// get attachment
		$attachment = $this->Attachment->getFile($id);

		// lets check if the attachment belongs to a Policy review
		if (empty($attachment) || !in_array($attachment['Attachment']['model'], array('SecurityPolicyReview', 'Review'))) {
			throw new ForbiddenException(__('You are not allowed to download this attachment.'));
		}

		// check LDAP group access via Review record
		$this->loadModel('Review');
		$review = $this->Review->find('first', array(
			'conditions' => array(
				'Review.id' => $attachment['Attachment']['foreign_key']
			),
			'recursive' => -1
		));

		// we throw forbidden exception if something doesnt check out correctly
		if (empty($review) || $review['Review']['model'] != 'SecurityPolicy') {
			throw new ForbiddenException(__('You are not allowed to download this attachment.'));
		}

		$policyId = $review['Review']['foreign_key'];

		// if ldap group access is required to be checked
		if (!$this->allowedForLogged && !$this->checkLdapGroupAccess($policyId)) {
			throw new ForbiddenException(__('You are not allowed to download this attachment.'));
		}

		// download the attachment
		return $this->AttachmentsMgt->download($id);
	}

	/**
	 * Get common document conditions.
	 */
	private function getConditions($data) {
		$conds = array(
			'SecurityPolicy.id' => $data['SecurityPolicy']['id'],
			'SecurityPolicy.status' => 1,
			'SecurityPolicy.permission !=' => SECURITY_POLICY_PRIVATE
		);

		return $conds;
	}

}