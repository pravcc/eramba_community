<?php
App::uses('AttachmentsAppController', 'Attachments.Controller');
App::uses('ThirdPartyAuditsModule', 'ThirdPartyAudits.Lib');
App::uses('VendorAssessmentsModule', 'VendorAssessments.Lib');
App::uses('AccountReviewsModule', 'AccountReviews.Lib');
App::uses('AttachmentsHelper', 'Attachments.View/Helper');
App::uses('Attachment', 'Attachments.Model');
App::uses('NotificationSystemManager', 'NotificationSystem.Lib');
App::uses('NotificationSystemSubject', 'NotificationSystem.Lib');
App::uses('WidgetListener', 'Widget.Controller/Crud/Listener');

class AttachmentsController extends AttachmentsAppController
{
	public $components = [
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'view' => 'Attachments./Attachments/index'
				],
				'indexTmp' => [
					'className' => 'AppIndex',
					'viewVar' => 'data',
					'view' => 'Attachments./Attachments/index'
				],
				'add' => [
					'useModal' => false,
				],
				'addTmp' => [
					'className' => 'AppAdd',
					'useModal' => false,
				],
			],
			'listeners' => ['Api', 'ApiPagination']
		],
	];
	public $helpers = ['Attachments.Attachments'];

	public $uses = ['Attachments.Attachment'];

	protected $_appControllerConfig = [
		'components' => [
		],
		'helpers' => [
		],
		'elements' => [
		]
	];

	protected $_isApi = false;

	public function beforeFilter()
	{
		if (!isset($this->request->params['ext']) ||
			$this->request->params['ext'] !== 'json') {
			$this->Crud->removeListener('Api');
			$this->Crud->removeListener('ApiPagination');
		} else {
			$this->_isApi = true;
		}

		$this->Crud->enable(['index', 'add', 'delete']);

		parent::beforeFilter();

		$this->title = __('Attachments');
		$this->subTitle = __('');

		//allows action if session key for that authentication is set
		if (AppModule::loaded('VendorAssessments')) {
			VendorAssessmentsModule::allowAction($this);
		}

		if (AppModule::loaded('AccountReviews')) {
			AccountReviewsModule::allowAction($this);
		}

		if (in_array($this->request->params['action'], ['add', 'addTmp'])) {
			$this->Security->csrfCheck = false;
		}

		$this->_checkAssessmentPermissions();
	}

	public function index($model, $foreignKey)
	{
		if (empty($model) || empty($foreignKey)) {
			throw new NotFoundException();
		}

		$this->Crud->on('beforePaginate', [$this, '_beforePaginate']);

		$this->set('model', $model);
		$this->set('foreignKey', $foreignKey);

		return $this->Crud->execute();
	}

	public function _beforePaginate(CakeEvent $event)
	{
		if ($event->subject->request->params['action'] == 'indexTmp') {
			$conditions = [
				'Attachment.hash' => $event->subject->request->params['pass'][0],
			];
		}
		else {
			$conditions = [
				'Attachment.model' => $event->subject->request->params['pass'][0],
				'Attachment.foreign_key' => $event->subject->request->params['pass'][1],
			];
		}

		$event->subject->paginator->settings['limit'] = 9999;
		$event->subject->paginator->settings['maxLimit'] = 9999;
		$event->subject->paginator->settings['conditions'] = $conditions;
		$event->subject->paginator->settings['order'] = [
			'Attachment.created' => 'DESC'
		];
	}

	public function indexTmp($hash)
	{
		if (empty($hash)) {
			throw new NotFoundException();
		}

		$this->Crud->on('beforePaginate', [$this, '_beforePaginate']);

		$this->set('hash', $hash);

		return $this->Crud->execute();
	}

	public function add($model, $foreignKey = null)
	{
		if (empty($model)) {
			throw new NotFoundException();
		}

		if ($foreignKey !== null) {
			$data = [
				'model' => $model,
				'foreign_key' => $foreignKey,
				'type' => Attachment::TYPE_NORMAL,
			];
		}
		else {
			$data = [
				'hash' => $model,
				'type' => Attachment::TYPE_TMP,
			];
		}

		return $this->_add($data);
	}

	public function addTmp($hash)
	{
		if (empty($hash)) {
			throw new NotFoundException();
		}

		return $this->_add([
			'hash' => $hash,
			'type' => Attachment::TYPE_TMP,
		]);
	}

	protected function _add($data = [])
	{
		$data = array_merge([
			'file' => $this->request->params['form']['file'],
			'user_id' => $this->logged['id'],
			'name' => ClassRegistry::init('Attachments.Attachment')->formatDisplayName($this->request->params['form']['file']['name']),
		], $data);

		$this->request->data['Attachment'] = $data;

		$this->Crud->on('afterSave', [$this, '_afterSave']);
		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function _afterSave(CakeEvent $event)
	{
		if ($event->subject->success) {
			$model = $event->subject->model;
			$id = $event->subject->id;

			$attachments = $model->find('first', [
				'conditions' => [
					$model->alias . '.' . $model->primaryKey => $id
				],
				'fields' => [
					'type',
					'model',
					'foreign_key'
				],
				'recursvie' => -1
			]);

			if ($attachments['Attachment']['type'] == Attachment::TYPE_NORMAL) {
				// trigger notification
				ClassRegistry::init($attachments['Attachment']['model'])->triggerNotification(
					'attachments',
					$attachments['Attachment']['foreign_key'],
					[
						'filename' => $event->subject->request->data['Attachment']['file']['name']
					]
				);

				ClassRegistry::init($attachments['Attachment']['model'])->triggerNotification(
					'widget_object',
					$attachments['Attachment']['foreign_key'],
					[
						'widget_object_type' => __('Attachment'),
            			'widget_object_content' => $event->subject->request->data['Attachment']['file']['name'],
					]
				);

				WidgetListener::deleteItemCache($attachments['Attachment']['model'], $attachments['Attachment']['foreign_key']);
			}
		}
	}

	public function _beforeRender(CakeEvent $event)
	{
		// If an attachment was added through API, return CRUDs json rather then this
		if ($this->request->is('api') && $this->_isApi) {
			return;
		}

		$this->RequestHandler->responseType('json');
		$this->YoonityJSConnector->deny();

		$response = [];

		if ($event->subject->success) {
			$response['element'] = $this->_getAttachmentItemRender($event->subject->id);

			$this->_widgetView($event);
		}
		else {
			$event->subject->response->statusCode(400);

			if (!empty($this->Attachment->validationErrors['file'])) {
				$response = $this->Attachment->validationErrors['file'];
			}
			else {
				$response = __('Cannot upload file.');
			}
		}

		$this->set($response);
		$this->set('_serialize', array_keys($response));
	}

	protected function _getAttachmentItemRender($id)
	{
		$data = $this->Attachment->find('first', [
			'conditions' => [
				'Attachment.id' => $id
			]
		]);

		if (empty($data)) {
			return false;
		}

		$view = new View($this);
		$view->set('logged', $this->logged);

		$Helper = new AttachmentsHelper($view);

		return $Helper->renderItem($data);
	}

	protected function _widgetView(CakeEvent $event)
	{
		if ($event->subject->request->params['action'] !== 'add') {
			return;
		}

		$Model = ClassRegistry::init($event->subject->request->params['pass'][0]);
		$foreignKey = $event->subject->request->params['pass'][1];

		if ($Model->Behaviors->enabled('Widget.Widget')) {
			$Model->widgetView($foreignKey, false, true);
		}
	}

	public function delete($id)
	{
		$this->Crud->on('beforeDelete', [$this, '_beforeDelete']);
		$this->Crud->on('afterDelete', [$this, '_afterDelete']);
		$this->Crud->on('beforeRender', [$this, '_beforeDeleteRender']);

		return $this->Crud->execute();
	}

	public function _beforeDelete(CakeEvent $event)
	{
		$attachment = ClassRegistry::init('Attachments.Attachment')->find('first', [
			'conditions' => [
				'Attachment.id' => $event->subject->id
			],
			'fields' => [
				'model',
				'foreign_key',
			],
			'recursvie' => -1
		]);

		if (!empty($attachment)) {
			WidgetListener::deleteItemCache($attachment['Attachment']['model'], $attachment['Attachment']['foreign_key']);
		}
	}

	public function _afterDelete(CakeEvent $event)
	{
		if ($event->subject->success) {
			$this->Attachment->logAttachment($event->subject->id, true);
		}
	}

	public function _beforeDeleteRender(CakeEvent $event)
	{
		$this->Modals->changeConfig('footer.buttons.deleteBtn.options.data-yjs-on-success-reload', '#attachments-list|.widget-story-list');
	}
}
