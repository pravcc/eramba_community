<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class ProjectExpensesController extends AppController
{
	public $helpers = [];
	public $components = [
		'Search.Prg', 'AdvancedFilters', 'Paginator', 'ObjectStatus.ObjectStatus',
		//'Visualisation.Visualisation',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
			],
			'listeners' => [
				'Api', 'ApiPagination', '.SubSection', 'BulkActions.BulkActions', 'Widget.Widget', 'Visualisation.Visualisation',
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem', 
						'Reports.Reports',
					]
				]
			]
		],
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

		$this->title = __('Project Expenses');
		$this->subTitle = __('This is the list of expenses for a given project.');
	}

	public function index( $project_id = null ) {
		$this->title = __('List of Expenses');

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Project Expense.');

		return $this->Crud->execute();
	}

	public function add($project_id = null) {
		$this->title = __('Create a Project Expense');
		$this->initAddEditSubtitle();

		// $project_id = (int) $project_id;

		// $this->set('project_id', $project_id);

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit a Project Expense');
		$this->initAddEditSubtitle();

		// $data = $this->ProjectExpense->find('first', array(
		// 	'conditions' => array(
		// 		'ProjectExpense.id' => $id
		// 	),
		// 	'recursive' => -1
		// ));
		// if (empty($data)) {
		// 	throw new NotFoundException();
		// }

		// $this->set('project_id', $data['ProjectExpense']['project_id']);
		// $this->set('id', $id);

		return $this->Crud->execute();
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('Use this form to create or edit new improvement expense. In this way you can control financial expenses on your projects.');
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
