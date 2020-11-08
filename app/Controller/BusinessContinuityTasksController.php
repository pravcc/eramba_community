<?php
App::uses('AppController', 'Controller');

class BusinessContinuityTasksController extends AppController {
	
	public $helpers = [];
	public $components = [
		'Paginator',// 'Visualisation.Visualisation',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
			],
			'listeners' => [
				'Api', 'ApiPagination', '.SubSection', 'BulkActions.BulkActions', 'Widget.Widget',
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem',
						'CustomFields.CustomFields',
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
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'history', 'restore', 'trash']);

		parent::beforeFilter();

		$this->title = __('Business Continuity Task');
		$this->subTitle = __('');
	}

	public function index() {
		$this->title = __('Business Continuity Tasks');

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Business Continuity Task.');
		
		return $this->Crud->execute();
	}

	public function add($planId = null) {
		$this->title = __('Create a Business Continuity Task');
		$this->initAddEditSubtitle();

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit a Business Continuity Task');

		unset($this->request->data['BusinessContinuityTask']['business_continuity_plan_id']);

		return $this->Crud->execute();
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('This is the tools used to create an emergency plan. Emergency plans are short and very much to the point. Have you noticed aircraft emergency plans? there\'s no point in writing long manuals since at emergency times there\'s no time to read. Keep it to the point and you\'ll do fine.');
	}

	public function history($id)
	{
		return $this->Crud->execute();
	}

	public function restore($autidId)
	{
		return $this->Crud->execute();
	}

	public function trash() {
		$this->title = __('Business Continuity Task (Trash)');

		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
    }
}
