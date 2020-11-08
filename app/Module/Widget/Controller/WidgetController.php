<?php
App::uses('WidgetAppController', 'Widget.Controller');
App::uses('ThirdPartyAuditsModule', 'ThirdPartyAudits.Lib');
App::uses('VendorAssessmentsModule', 'VendorAssessments.Lib');
App::uses('AccountReviewsModule', 'AccountReviews.Lib');
App::uses('WidgetListener', 'Widget.Controller/Crud/Listener');
App::uses('Comment', 'Comments.Model');
App::uses('Attachment', 'Attachments.Model');

class WidgetController extends WidgetAppController {

	public $components = [
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'Widget.Widget'
				],
			],
			'listeners' => [
				'Widget.Widget',
			]
		],
	];
	public $helpers = ['Comments.Comments', 'Attachments.Attachments', 'LimitlessTheme.TabsComponent'];

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
		parent::beforeFilter();

		$this->title = __('Comments & Attchments');
		$this->subTitle = __('');

		//allows action if session key for that authentication is set
		if (AppModule::loaded('VendorAssessments')) {
			VendorAssessmentsModule::allowAction($this);
		}

		if (AppModule::loaded('AccountReviews')) {
			AccountReviewsModule::allowAction($this);
		}

		$this->_checkAssessmentPermissions();
	}

	public function story($model, $foreignKey = null)
	{
		if ($foreignKey !== null) {
			$commentsConditions = [
				'Comment.type' => Comment::TYPE_NORMAL,
				'Comment.model' => $model,
				'Comment.foreign_key' => $foreignKey
			];

			$attachmentsConditions = [
				'Attachment.type' => Attachment::TYPE_NORMAL,
				'Attachment.model' => $model,
				'Attachment.foreign_key' => $foreignKey
			];
		}
		else {
			$commentsConditions = [
				'Comment.type' => Comment::TYPE_TMP,
				'Comment.hash' => $model,
			];

			$attachmentsConditions = [
				'Attachment.type' => Attachment::TYPE_TMP,
				'Attachment.hash' => $model,
			];
		}

		$comments = ClassRegistry::init('Comments.Comment')->find('all', [
			'conditions' => $commentsConditions,
			'contain' => ['User'],
			'limit' => 9999,
			'maxLimit' => 9999,
			'order' => ['Comment.created' => 'DESC']
		]);

		$attachments = ClassRegistry::init('Attachments.Attachment')->find('all', [
			'conditions' => $attachmentsConditions,
			'contain' => ['User'],
			'limit' => 9999,
			'maxLimit' => 9999,
			'order' => ['Attachment.created' => 'DESC']
		]);

		$this->set('comments', $comments);
		$this->set('attachments', $attachments);
	}

	public function index($model, $foreignKey)
	{
		if (empty($model) || empty($foreignKey)) {
			throw new NotFoundException();
		}
		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		$this->Crud->useModel($model);
		$this->Crud->enable('index');
        
        return $this->Crud->execute();
	}

	public function _beforeRender(CakeEvent $event)
	{
		$Model = $event->subject->model;
		$foreignKey = $event->subject->request->params['pass'][1];

		$this->set('CommentFieldDataCollection', ClassRegistry::init('Comments.Comment')->getFieldCollection());
		
		$this->_widgetView($Model, $foreignKey);

		$title = $Model->getRecordTitle($foreignKey);
		if ($title) {
			$this->title = __('Comments & Attachments for "%s"', $title);
		}

		$this->Modals->setHeaderHeading($this->title);

		if (isset($this->request->query['reload'])) {
			$this->Modals->changeConfig('footer.buttons.closeBtn.options.data-yjs-on-complete', $this->request->query['reload']);
			$this->Modals->changeConfig('footer.buttons.closeBtn.options.data-yjs-on-modal-close', $this->request->query['reload']);
		}
	}

	protected function _widgetView($Model, $foreignKey)
	{
		if ($Model->Behaviors->enabled('Widget.Widget')) {
			$Model->widgetView($foreignKey);
			WidgetListener::deleteItemCache($Model->modelFullName(), $foreignKey);
		}
	}
}
