<?php
App::uses('AppController', 'Controller');
App::uses('CakeText', 'Utility');

/**
 * @section
 */
class SecurityPoliciesController extends AppController
{
	public $helpers = ['ImportTool.ImportTool', 'UserFields.UserField', 'Attachments.Attachments'];
	public $components = [
		'Paginator', 'RequestHandler', 'Pdf', 'ObjectStatus.ObjectStatus',
		'Ajax' => [
			'actions' => ['add', 'edit', 'delete'],
			'modules' => ['comments', 'records', 'attachments', 'notifications']
		],
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
			],
			'listeners' => [
				'Api', 'ApiPagination', 'BulkActions.BulkActions', 'Widget.Widget',
				'Visualisation.Visualisation',
				'Taggable.Taggable' => [
					'fields' => ['Tag']
				],
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem',
						'CustomFields.CustomFields',
						'Reports.Reports',
					]
				]
			]
		],
		//'Visualisation.Visualisation',
		'ReviewsPlanner.Reviews',
		'ObjectStatus.ObjectStatus',
		'UserFields.UserFields' => [
			'fields' => ['Owner', 'Collaborator']
		]
	];

	protected $_appControllerConfig = [
		'components' => [
		],
		'helpers' => [
		],
		'elements' => [
		]
	];

	public function beforeFilter() {
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'trash', 'history', 'restore']);

		parent::beforeFilter();

		$this->Security->unlockedActions = array('ldapGroups');

		$this->title = __('Security Policies');
		$this->subTitle = __('Manage all documents in the scope of your GRC program');
	}

	public function index() {
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');
		$this->Crud->addListener('ObjectStatus', 'ObjectStatus.ObjectStatus');

		$this->initOptions();

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Security Policy.');

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Security Policy');
		$this->subTitle = __('Record and manage your program security incidents. Incidents can be linked to controls, assets and third parties in order to make it clear what components of the program have been affected.');

		// $this->initOptions();

		$this->Crud->on('beforeRender', [$this, '_beforeAddEditRender']);
		$this->Crud->on('afterSave', array($this, '_afterSave'));

		$this->_setAttachmentData();

		return $this->Crud->execute();
	}

	public function _setAttachmentData()
	{
		if ($this->request->is('get') || $this->Session->read('SecurityPolicy.Attachment.hash') === null) {
			$this->Session->write('SecurityPolicy.Attachment.hash', CakeText::uuid());
		}

		$attachmentHash = $this->Session->read('SecurityPolicy.Attachment.hash');
		$this->set('attachmentHash', $attachmentHash);
	}

	public function _beforeAddEditRender(CakeEvent $e)
	{
		$subject = $e->subject;
		$controller = $subject->controller;
		$action = $subject->crud->action();

		if ($this->_FieldDataCollection->has('next_review_date')) {
			$nextReviewDate = $this->_FieldDataCollection->get('next_review_date');
			$nextReviewDate->toggleEditable(true);
		}

		if ($this->_FieldDataCollection->has('version')) {
			$version = $this->_FieldDataCollection->get('version');
			$version->toggleEditable(true);
		}

		// // if current action is Add action
		if ($action instanceof AddCrudAction) {
			$controller->set('disabledReviewFields', false);
			// 	$nextReviewDate->toggleEditable(true);
		}

		if ($action instanceof EditCrudAction) {
			$controller->set('disabledReviewFields', true);
			// 	$nextReviewDate->toggleEditable(true);

			// 	$config = $nextReviewDate->config();
			// 	// $this->_FieldDataCollection->remove('next_review_date');
			// 	$config['renderHelper'] = ['SecurityPolicies', 'nextReviewDateDisabled'];

			// 	$this->_FieldDataCollection->add('next_review_date', $config);
		}
	}

	public function _afterSave(CakeEvent $event) {
		// ddd($this->SecurityPolicy->validationErrors);
		if ($event->subject->success) {
			// $this->Flash->set(
			// 	__('We have created two reviews for this policy, one with todays date, another with the date in the future where you plan to review this policy. Remember, if you used "Attachments" as content you must attach your policy documents in the review we created for today.'),
			// 	[
			// 		'key' => 'info',
			// 		'params' => [
			// 			'renderTimeout' => 1500
			// 		]
			// 	]
			// );

			$this->_handleReviewAttachments($event);
		}
	}

	protected function _handleReviewAttachments(CakeEvent $event)
	{
		$SecurityPolicyReview = ClassRegistry::init('SecurityPolicyReview');
		$review = $SecurityPolicyReview->find('first', [
			'conditions' => [
				'SecurityPolicyReview.model' => 'SecurityPolicy',
				'SecurityPolicyReview.foreign_key' => $event->subject->id
			],
			'order' => [
				'SecurityPolicyReview.planned_date' => 'ASC',
				'SecurityPolicyReview.id' => 'ASC',
			],
			'contain' => []
		]);

		if (!empty($review) && $this->Session->read('SecurityPolicy.Attachment.hash') !== null) {
			$Attachment = ClassRegistry::init('Attachments.Attachment');
			$Attachment->tmpToNormal(
				$this->Session->read('SecurityPolicy.Attachment.hash'), 
				'SecurityPolicyReview', 
				$review['SecurityPolicyReview']['id']
			);
		}

		$this->Session->delete('SecurityPolicy.Attachment.hash');
	}

	public function edit( $id = null ) {
		$id = (int) $id;

		$this->title = __('Edit a Security Policy');
		$this->initAddEditSubtitle();

		// $this->initOptions(true);

		$this->Crud->on('beforeHandle', [$this, '_beforeAddEditRender']);
		$this->Crud->on('beforeSave', array($this, '_beforeSave'));

		return $this->Crud->execute();
	}

	public function _beforeSave(CakeEvent $event) {
		$data = $this->SecurityPolicy->find('first', array(
			'conditions' => array(
				'SecurityPolicy.id' => $event->subject->id
			),
			'recursive' => 1
		));

		if (empty($data)) {
			throw new NotFoundException();
		}

		$this->request->data['SecurityPolicy']['version'] = $data['SecurityPolicy']['version'];
		$this->request->data['SecurityPolicy']['next_review_date'] = $data['SecurityPolicy']['next_review_date'];
		$this->request->data['SecurityPolicy']['url'] = $data['SecurityPolicy']['url'];
		$this->request->data['SecurityPolicy']['description'] = $data['SecurityPolicy']['description'];
		$this->request->data['SecurityPolicy']['use_attachments'] = $data['SecurityPolicy']['use_attachments'];
		unset($this->SecurityPolicy->validate['next_review_date']);
	}


	public function trash()
	{
	    $this->set('title_for_layout', __('Security Policies (Trash)'));
	    $this->set('subtitle_for_layout', __('This is the list of security policies.'));

	    $this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

	    return $this->Crud->execute();
	}

	private function initOptions($disabledReviewFields = false) {
		$this->set('disabledReviewFields', $disabledReviewFields);

		$ldapConnectors = $this->SecurityPolicy->getConnectors();
		
		$this->set('ldapConnectors', $ldapConnectors);
	}

	private function initAddEditSubtitle() {
		$this->subTitle = false;
	}

	/**
	 * Create/get direct url for a document.
	 *
	 * @param  int $id Security Policy ID.
	 */
	public function getDirectLink($id) {
		$this->allowOnlyAjax();

		$data = $this->SecurityPolicy->find('first', array(
			'conditions' => array(
				'SecurityPolicy.id' => $id
			),
			'fields' => array('index', 'hash'),
			'recursive' => -1
		));

		if (empty($data)) {
			throw new NotFoundException();
		}

		if (empty($data['SecurityPolicy']['hash'])) {
			$rand = mt_rand(1, 999999) . $data['SecurityPolicy']['index'] . $id;
			$hash = sha1($rand);

			$this->SecurityPolicy->id = $id;
			if (!$this->SecurityPolicy->saveField('hash', $hash, false)) {
				echo json_encode(array(
					'success' => false,
					'message' => __('Error occured while creating a direct url. Please try again.')
				));
				return false;
			}
		}
		else {
			$hash = $data['SecurityPolicy']['hash'];
		}

		$this->Modals->init();
            
        $this->Modals->setHeaderHeading(__('Direct Link'));

		$url = Router::url(array('plugin' => null, 'controller' => 'policy', 'action' => 'documentDirect', $hash), true);
		$this->set('directLink', $url);
	}

	/**
	 * Create a copy of a document.
	 *
	 * @param  int $id Security Policy ID.
	 */
	public function duplicate($id) {
		$document = $this->SecurityPolicy->find('first', array(
			'conditions' => array(
				'SecurityPolicy.id' => $id
			),
			'contain' => $this->UserFields->attachFieldsToArray(['Owner', 'Collaborator'], array(
				'Project' => array(
					'fields' => array('id')
				),
				'RelatedDocuments' => array(
					'fields' => array('id')
				),
				'SecurityPolicyLdapGroup' => array(
					'fields' => array('name')
				),
				'Tag' => array(
					'fields' => array('id', 'title')
				)
			))
		));

		if (empty($document)) {
			throw new NotFoundException();
		}

		//remove data we dont want to save
		unset($document['SecurityPolicy']['id']);
		unset($document['SecurityPolicy']['created']);
		unset($document['SecurityPolicy']['modified']);

		$document['SecurityPolicy']['Project'] = array();
		foreach ($document['Project'] as $item) {
			$document['SecurityPolicy']['Project'][] = $item['id'];
		}

		$document['SecurityPolicy']['Owner'] = array();
		foreach ($document['Owner'] as $item) {
			$document['SecurityPolicy']['Owner'][] = $item['id'];
		}

		$document['SecurityPolicy']['Collaborator'] = array();
		foreach ($document['Collaborator'] as $item) {
			$document['SecurityPolicy']['Collaborator'][] = $item['id'];
		}

		$document['SecurityPolicy']['RelatedDocuments'] = array();
		foreach ($document['RelatedDocuments'] as $item) {
			$document['SecurityPolicy']['RelatedDocuments'][] = $item['id'];
		}

		$document['SecurityPolicy']['SecurityPolicyLdapGroup'] = array();
		foreach ($document['SecurityPolicyLdapGroup'] as $item) {
			$document['SecurityPolicy']['SecurityPolicyLdapGroup'][] = $item['id'];
		}
		
		$tags = array();
		foreach ($document['Tag'] as $item) {
			$tags[] = $item['title'];
		}

		//we change the title to Copy of ...
		$document['SecurityPolicy']['index'] = __('Copy of %s', $document['SecurityPolicy']['index']);

		$this->SecurityPolicy->set($document['SecurityPolicy']);

		$dataSource = $this->SecurityPolicy->getDataSource();
		$dataSource->begin();

		$ret = $this->SecurityPolicy->save(null, false);

		$ret &= $this->SecurityPolicy->Tag->saveTagsArr($tags, 'SecurityPolicy', $this->SecurityPolicy->id);

		if ($ret) {
			$dataSource->commit();
			$this->Flash->success(__('Security Policy was successfully cloned.'));
		}
		else {
			$dataSource->rollback();
			$this->Session->setFlash(__('Error occured while saving the cloned data. Please try it again.'), FLASH_ERROR);
		}
	}

	/**
	 * Render a multiselect with LDAP groups.
	 *
	 * @param  int $id LDAP Connector ID.
	 */
	public function ldapGroups($id) {
		$this->allowOnlyAjax();

		$this->YoonityJSConnector->deny();

		$ldap = $this->Components->load('LdapConnectorsMgt');
		$ldap->initialize($this);

		$LdapConnector = $ldap->getConnector($id);
		$ldapConnection = $LdapConnector->connect();

		$groups = $LdapConnector->getGroupList();

		$this->set('groups', $groups);
		$this->set('ldapConnection', $ldapConnection);

		$this->render('/Elements/securityPolicies/ldapGroupsField');
	}

	/**
	 * Sends email notifications to users that has permission to this doc.
	 */
	public function sendNotifications($securityPolicyId) {
		$this->set('title_for_layout', __('Send Notifications'));
		$this->set('subtitle_for_layout', __('Send email to all people that has permission to this Policy.'));

		$data = $this->SecurityPolicy->find('first', array(
			'conditions' => array(
				'SecurityPolicy.id' => $securityPolicyId,
				'SecurityPolicy.permission' => SECURITY_POLICY_LOGGED
			),
			'contain' => array(
				'LdapConnector',
				'SecurityPolicyLdapGroup'
			)
		));

		if (empty($data)) {
			$this->actionUnavailable(array('controller' => 'securityPolicies', 'action' => 'index'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			if (isset($this->request->data['SecurityPolicyNotification']['send']) && !empty($this->request->data['SecurityPolicyNotification']['send']) && is_array($this->request->data['SecurityPolicyNotification']['send']))  {

				$users = $this->request->data['SecurityPolicyNotification']['send'];

				$dataSource = $this->SecurityPolicy->getDataSource();
				$dataSource->begin();

				$ret = $this->SecurityPolicy->saveNotificationLog($securityPolicyId, $users);

				$ret &= $this->sendNotificationEmail($users, array(
					'policy' => $data,
					'portalUrl' => Router::url(array('plugin' => null, 'controller' => 'policy', 'action' => 'index'), true),
					'documentUrl' => Router::url(array('plugin' => null, 'controller' => 'policy', 'action' => 'index', $securityPolicyId), true)
				));

				if ($ret) {
					
					$dataSource->commit();

					$this->Session->setFlash(__('Notifications were successfully sent.'), FLASH_OK);
					$this->redirect(array('controller' => 'securityPolicies', 'action' => 'index'));
				}
				else {
					$dataSource->rollback();
					$this->Session->setFlash(__('Error occured. Please try it again.'), FLASH_ERROR);
				}
			}
			else {
				$this->Session->setFlash(__('Please choose one or more users to send notification.'), FLASH_ERROR);
			}
		}
		else {
			
		}
		
		$this->set('allowedUsers', $this->getAllowedUsers($data));
		$this->set('data', $data);
		$this->set('securityPolicyId', $securityPolicyId);
	}

	private function getAllowedUsers($data) {

		if (empty($data['SecurityPolicyLdapGroup'])) {
			throw new NotFoundException();
		}

		$connector = $data['LdapConnector'];

		$groups = array();
		foreach ($data['SecurityPolicyLdapGroup'] as $group) {
			$groups[] = $group['name'];
		}

		$ldap = $this->Components->load('LdapConnectorsMgt');
		$ldap->initialize($this);

		$LdapConnector = $ldap->getConnector($connector);
		$ldapConnection = $LdapConnector->connect();

		return $LdapConnector->getGrouppedEmailsList($groups);

		/*$LdapConnectorsMgt = $this->Components->load('LdapConnectorsMgt');
		$LdapConnectorsMgt->initialize($this);
		// $ldapConnection = $ldap->ldapConnect($connector['host'], $connector['port'], $connector['ldap_bind_dn'], $connector['ldap_bind_pw']);


		$this->loadModel('LdapConnectorAuthentication');
		$data = $this->LdapConnectorAuthentication->find('first', array(
			'recursive' => 0
		));

		$groupsUsers = $ldap->getUserEmailsByGroups($connector, $groups, $data['AuthPolicies']);
		
		return $groupsUsers;*/
	}

	private function sendNotificationEmail($email, $emailData) {
		if (empty($email)) {
			return true;
		}

		$_subject = __('Policy Document');
		$email = $this->initEmail($email, $_subject, 'security_policy_notification', $emailData);
		return $email->send();
	}

	public function history($id)
	{
		return $this->Crud->execute();
	}

	public function restore($autidId)
	{
		return $this->Crud->execute();
	}
	
}
