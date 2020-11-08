<?php
App::uses('CommentsAppController', 'Comments.Controller');
App::uses('ThirdPartyAuditsModule', 'ThirdPartyAudits.Lib');
App::uses('VendorAssessmentsModule', 'VendorAssessments.Lib');
App::uses('AccountReviewsModule', 'AccountReviews.Lib');
App::uses('NotificationSystemManager', 'NotificationSystem.Lib');
App::uses('NotificationSystemSubject', 'NotificationSystem.Lib');
App::uses('WidgetListener', 'Widget.Controller/Crud/Listener');
App::uses('Comment', 'Comments.Model');

class CommentsController extends CommentsAppController
{

	public $components = [
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'view' => 'Comments./Comments/index'
				],
				'add' => [
					'view' => 'Comments./Elements/Comments/add'
				],
			],
			'listeners' => ['Api', 'ApiPagination']
		],
	];
	public $helpers = ['Comments.Comments'];

	public $uses = ['Comments.Comment'];

	protected $_appControllerConfig = [
		'components' => [
		],
		'helpers' => [
		],
		'elements' => [
		]
	];

	public function beforeFilter()
	{
		$this->Crud->enable(['index', 'add', 'delete']);

		parent::beforeFilter();

		$this->title = __('Comments');
		$this->subTitle = __('');

		//allows action if session key for that authentication is set
		if (AppModule::loaded('VendorAssessments')) {
			VendorAssessmentsModule::allowAction($this);
		}

		if (AppModule::loaded('AccountReviews')) {
			AccountReviewsModule::allowAction($this);
		}

		if (in_array($this->request->params['action'], ['add'])) {
			$this->Security->csrfCheck = false;
		}

		$this->_checkAssessmentPermissions();
	}

	public function index($model, $foreignKey)
	{
		// not used function for now
	}

	public function add($model, $foreignKey = null)
	{
		$this->Modals->changeConfig('layout', 'clean');

		if ($foreignKey !== null) {
			$data = [
				'model' => $model,
				'foreign_key' => $foreignKey,
				'type' => Comment::TYPE_NORMAL,
				'user_id' => $this->logged['id']
			];
		}
		else {
			$data = [
				'hash' => $model,
				'type' => Comment::TYPE_TMP,
				'user_id' => $this->logged['id']
			];
		}

		$this->request->data['Comment'] = array_merge($this->request->data['Comment'], $data);

		$this->Crud->on('afterSave', [$this, '_afterSave']);
		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function _afterSave(CakeEvent $event)
	{
		if ($event->subject->success) {
			$model = $event->subject->model;
			$id = $event->subject->id;

			$comment = $model->find('first', [
				'conditions' => [
					'Comment.id' => $id
				],
				'fields' => [
					'type',
					'model',
					'foreign_key',
					'message'
				],
				'recursvie' => -1
			]);

			if ($comment['Comment']['type'] == Comment::TYPE_NORMAL) {
				// trigger notification
				ClassRegistry::init($comment['Comment']['model'])->triggerNotification(
					'comments',
					$comment['Comment']['foreign_key'],
					[
						'comment_message' => $comment['Comment']['message']
					]
				);

				ClassRegistry::init($comment['Comment']['model'])->triggerNotification(
					'widget_object',
					$comment['Comment']['foreign_key'],
					[
						'widget_object_type' => __('Comment'),
            			'widget_object_content' => $comment['Comment']['message'],
					]
				);

				WidgetListener::deleteItemCache($comment['Comment']['model'], $comment['Comment']['foreign_key']);
			}
		}
	}

	public function _beforeRender(CakeEvent $event)
	{
		if (!empty($this->request->params['pass'][1])) {
			$Model = ClassRegistry::init($event->subject->request->params['pass'][0]);
			$foreignKey = $event->subject->request->params['pass'][1];

			if (!empty($event->subject->success)) {
				$this->_widgetView($Model, $foreignKey);
			}
		}

		if (!empty($event->subject->success)) {
			$this->request->data = [];
		}
	}

	protected function _widgetView($Model, $foreignKey)
	{
		if ($Model->Behaviors->enabled('Widget.Widget')) {
			$Model->widgetView($foreignKey, true, false);
			WidgetListener::deleteItemCache($Model->modelFullName(), $foreignKey);
		}
	}

	public function delete($id)
	{
		$this->Crud->on('beforeRender', [$this, '_beforeDeleteRender']);
		$this->Crud->on('beforeDelete', [$this, '_beforeDelete']);

		return $this->Crud->execute();
	}

	public function _beforeDelete(CakeEvent $event)
	{
		$comment = ClassRegistry::init('Comments.Comment')->find('first', [
			'conditions' => [
				'Comment.id' => $event->subject->id
			],
			'fields' => [
				'model',
				'foreign_key',
			],
			'recursvie' => -1
		]);

		if (!empty($comment)) {
			WidgetListener::deleteItemCache($comment['Comment']['model'], $comment['Comment']['foreign_key']);
		}
	}

	public function _beforeDeleteRender(CakeEvent $event)
	{
		$this->Modals->changeConfig('footer.buttons.deleteBtn.options.data-yjs-on-success-reload', '#comments-list|.widget-story-list');
	}

}
