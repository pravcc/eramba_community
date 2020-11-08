<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class IssuesController extends AppController
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
				]
			],
			'listeners' => [
				'Api', 'ApiPagination', '.SubSection', 'Widget.Widget',
				'.ModuleDispatcher' => [
					'listeners' => 'NotificationSystem.NotificationSystem'
				]
			]
		],
		// 'Visualisation.Visualisation'
	);
	public $uses = array();

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

		$this->Crud->addListener('SubSection', 'SubSection');

		$this->issueModel = $this->Crud->getSubject()->model;
		$this->relatedModel = ClassRegistry::init($this->issueModel->parentModel());

		$this->Auth->allow();
		$this->Crud->enable('add', 'edit', 'delete', 'index');

		$this->Crud->on('beforeFilter', array($this, '_beforeFilter'));
		$this->Crud->on('beforeRender', array($this, '_beforeRender'));

		$this->title = __('Issues');
		$this->subTitle = __('TBD');
	}

	/**
	 * Before filter action that changes things to apply trash.
	 */
	public function _beforeFilter(CakeEvent $e)
	{
		// and attach an event to the advanced filter object to additionally make changes to the final query
		$AdvancedFiltersObject = $e->subject->AdvancedFiltersObject;
		$AdvancedFiltersObject->getEventManager()->attach(
			[$this, '_beforeFilterFind'],
			'AdvancedFilter.beforeFind',
			[
				// 'passParams' => true
			]
		);
	}

	/**
	 * This callback makes changes to the query which disables SoftDelete functionality
	 * 
	 * @param  array $query  Query array.
	 * @return array         Updated query.
	 */
	public function _beforeFilterFind(CakeEvent $e)
	{
		$model = $e->subject->getModel();
		$args = $this->listArgs();

		if ($args->foreignKey !== null) {
			$e->data[0]['conditions'][$model->alias . '.foreign_key'] = $args->foreignKey;
			if ($model->schema('model') !== null) {
				$e->data[0]['conditions'][$model->alias . '.model'] = $model->parentModel();
			}
		}
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
			$this->_initData($args->model, $args->foreignKey);
		}

		$this->Modals->changeConfig('footer.buttons.saveBtn.options.data-yjs-on-modal-close', '@reload-parent');
	}

	protected function _initData($model, $foreign_key) {
		$mainItem = $this->_getMainItem();
		$this->set('mainItem', $mainItem);
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
		$controller = $this;
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

		if ($action instanceof EditCrudAction) {
			$id = $pass[0];

			$review = $this->issueModel->find('first', [
				'conditions' => [
					$this->issueModel->alias . '.id' => $id
				],
				'fields' => [
					$this->issueModel->alias . '.foreign_key',
				],
				'recursive' => -1
			]);

			$foreignKey = $review[$this->issueModel->alias]['foreign_key'];
		}

		$args = new stdClass();
		$args->model = $this->relatedModel->alias;
		$args->reviewModel = $this->issueModel->alias;
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

	public function _beforeEditSave(CakeEvent $e)
	{
	}

	public function _afterSave(CakeEvent $event)
	{
	}

}