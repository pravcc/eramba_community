<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class LegalsController extends AppController
{
	public $components = [
		'Paginator',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				],
				'filter_test' => [
					'enabled' => true,
					'className' => 'AdvancedFilters.MultipleFilters'
				]
			],
			'listeners' => [
				'Api', 'ApiPagination', 'BulkActions.BulkActions', 'Widget.Widget',
				'Visualisation.Visualisation',
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem',
						'CustomFields.CustomFields',
						'Reports.Reports',
					]
				]
			]
		],
		'UserFields.UserFields' => [
			'fields' => ['LegalAdvisor']
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

		$this->title = __('Liabilities');
		$this->subTitle = __('Describes all liabilities in the scope of this GRC program');

		$this->Auth->allow('inline_edit', 'trigger_notifications');
	}

	public function history($id)
	{
		return $this->Crud->execute();
	}

	public function restore($autidId)
	{
		return $this->Crud->execute();
	}

	/**
	 * Prototype.
	 */
	/*public function show($id) {
		$data = $this->Legal->find('first', array(
			'conditions' => array(
				'Legal.id' => $id
			)
		));

		if (empty($data)) {
			throw new NotFoundException();
		}

		$this->set('title_for_layout', __('Legal Constrain'));
		$this->set('data', $data);
	}*/

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		// $this->Crud->addListener('Tooltips', 'Tooltips.Tooltips'); // Ready for later use

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Legal');

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Legal Constraint');
		$this->initAddEditSubtitle();

		$this->Crud->on('afterSave', [$this, '_afterSave']);

		return $this->Crud->execute();
	}

	public function _afterSave(CakeEvent $e)
	{
	}

	public function edit( $id = null ) {
		$this->title = __('Edit a Legal Constraint');
		$this->initAddEditSubtitle();

		$this->Crud->on('beforeRender', [$this, '_beforeEditRender']);

		return $this->Crud->execute();
	}

	public function _beforeEditRender(CakeEvent $e)
	{
		$isUsedInRisks = 0;
		if (isset($e->subject->request->params['pass'][0])) {
			$id = $e->subject->request->params['pass'][0];

			$isUsedInRisks = $e->subject->model->isUsedInRisks($id);
		}
		
		$this->set('isUsedInRisks', $isUsedInRisks);
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('This section allows you to define all applicable business liabilities. This will be used later in the risk management module to magnify those risks which are subject to them');
	}

	public function trash()
	{
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

}
