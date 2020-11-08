<?php
App::uses('AppController', 'Controller');

class ReviewsPlannerController extends AppController
{
	public $helpers = array();
	public $components = array(
		// reviews component handles correct model name configuration for CRUD
		// 'ReviewsManager',
		'Paginator', 
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				],
				'add' => [
					'enabled' => true
				],
				'edit' => [
					'enabled' => true
				],
				'delete' => [
					'enabled' => true
				],
				'trash' => [
					'enabled' => true
				],
				'history' => [
					'className' => 'ObjectVersion.History',
					'enabled' => true
				],
				'restore' => [
					'className' => 'ObjectVersion.Restore',
					'enabled' => true
				]
			]
		]
	);
	public $uses = array();

	protected $_appControllerConfig = [
		'components' => [
			'Crud.Crud' => [
				'listeners' => [
					'Api', 'ApiPagination', '.SubSection', 'Visualisation.Visualisation',
					'.ModuleDispatcher' => [
						'listeners' => [
							'NotificationSystem.NotificationSystem',
							'Reports.Reports',
						]
					],
					'Widget.Widget',
				]
			]
		]
	];

	public function beforeFilter()
	{
		parent::beforeFilter();

		$this->reviewModel = $this->Crud->getSubject()->model;
		$this->relatedModel = ClassRegistry::init($this->reviewModel->parentModel());

		$this->Crud->enable('add', 'edit', 'delete', 'index', 'trash', 'history', 'restore');
		
		$this->Crud->on('beforeRender', array($this, '_beforeRender'));
	}

	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function _beforeRender(CakeEvent $e)
	{
		$args = $this->listArgs();

		$action = $e->subject->crud->action();
		if ($action instanceof AddCrudAction) {
			$this->_initData($args->model, $args->foreignKey);
		}

		if ($action instanceof EditCrudAction) {
			$this->_initData($args->model, $args->foreignKey, $args->id);
		}
	}

	protected function _initData($model, $foreign_key, $reviewId = null) {
		$ReviewModelAlias = $this->reviewModel->alias;

		$reviewCompleted = false;
		if ($reviewId) {
			$reviewCompleted = $this->reviewModel->find('count', array(
				'conditions' => [
					$ReviewModelAlias . '.id' => $reviewId,
					$ReviewModelAlias . '.completed' => REVIEW_COMPLETE
				],
				'recursive' => -1
			));

			if ($reviewCompleted) {
				$this->Modals->changeConfig('footer.buttons.saveBtn.visible', false);
			}
		}

		$mainItem = $this->_getMainItem();
		$prevReview = $this->reviewModel->getLastCompletedReview($foreign_key);

		$futureConds = array(
			$ReviewModelAlias . '.foreign_key' => $foreign_key,
			$ReviewModelAlias . '.planned_date >' => date('Y-m-d'),
			$ReviewModelAlias . '.completed' => REVIEW_NOT_COMPLETE,
		);

		if (!empty($reviewId)) {
			$futureConds[$ReviewModelAlias . '.id !='] = $reviewId;
		}

		$futureReview = $this->reviewModel->find('first', array(
			'conditions' => $futureConds,
			'recursive' => -1
		));
		
		$this->set('reviewModel', $this->reviewModel->alias);
		$this->set('reviewCompleted', $reviewCompleted);
		$this->set('mainItem', $mainItem);
		$this->set('prevReview', $prevReview);
		$this->set('futureReview', $futureReview);
	}

	public function _getMainItem()
	{
		$args = $this->listArgs();

		return $this->relatedModel->find('first', array(
			'conditions' => array(
				'id' => $args->foreignKey
			),
			'recursive' => -1
		));
	}

	/**
	 * List arguments passed to this controller's action.
	 * 
	 * @return stdClass
	 */
	public function listArgs()
	{
		$action = $this->Crud->action();
		$model = $this->relatedModel->alias;

		$pass = $this->request->params['pass'];

		$foreignKey = null;
		$id = null;

		if ($action instanceof AdvancedFiltersCrudAction) {
			if (!empty($pass)) {
				$foreignKey = $pass[0];
			}
		}

		if ($action instanceof AddCrudAction) {
			if (isset($pass[0])) {
				$foreignKey = $pass[0];
			} elseif (isset($this->request->data[$this->reviewModel->alias]['foreign_key'])) {
				$foreignKey = $this->request->data[$this->reviewModel->alias]['foreign_key'];
			}
			
		}

		if ($action instanceof EditCrudAction) {
			$id = $pass[0];

			$review = $this->reviewModel->find('first', [
				'conditions' => [
					$this->reviewModel->alias . '.id' => $id
				],
				'fields' => [
					$this->reviewModel->alias . '.foreign_key',
				],
				'recursive' => -1
			]);

			if (!empty($review)) {
				$foreignKey = $review[$this->reviewModel->alias]['foreign_key'];
			}
		}

		$args = new stdClass();
		$args->model = $this->relatedModel->alias;
		$args->reviewModel = $this->reviewModel->alias;
		$args->foreignKey = $foreignKey;
		$args->id = $id;

		return $args;
	}

	public function index($foreignKey = null)
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function add($foreign_key = null)
	{
		$this->title = __('Create a Review');
		$this->Crud->on('afterSave', array($this, '_afterSave'));
		$this->Crud->on('beforeSave', array($this, '_beforeAddSave'));

		return $this->Crud->execute();
	}

	public function edit($id)
	{
		$this->title = __('Edit a Review');
		$this->Crud->on('afterSave', array($this, '_afterSave'));
		$this->Crud->on('beforeSave', array($this, '_beforeEditSave'));

		return $this->Crud->execute();
	}

	public function delete($id)
	{
		$this->title = __('Delete a Review');
		$this->Crud->on('afterDelete', array($this, '_afterSave'));

		return $this->Crud->execute();
	}

	public function _beforeAddSave(CakeEvent $e)
	{
		$subject = $e->subject;
		$request = $subject->request;
		$model = $subject->model;

		$request->data[$model->alias]['user_id'] = $subject->controller->logged['id'];
	}

	public function _beforeEditSave(CakeEvent $e)
	{
		$subject = $e->subject;
		$request = $subject->request;
		$model = $subject->model;

		$data = $model->find('first', [
			'conditions' => [
				'id' => $subject->id
			],
			'recursive' => -1
		]);

		$request->data[$model->alias]['planned_date'] = $data[$model->alias]['planned_date'];

		if ($data[$model->alias]['completed']) {
			$request->data[$model->alias]['actual_date'] = $data[$model->alias]['actual_date'];
			$request->data[$model->alias]['description'] = $data[$model->alias]['description'];
		}
	}

	public function _afterSave(CakeEvent $event)
	{
		if (!empty($event->subject->success)) {
			$this->loadModel('Review');
			$this->Review->triggerAssociatedObjectStatus($event->subject->id);
		}
	}

	public function trash()
	{
	    $this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

	    return $this->Crud->execute();
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