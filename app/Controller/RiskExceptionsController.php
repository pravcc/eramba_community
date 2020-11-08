<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class RiskExceptionsController extends AppController
{
	public $helpers = ['ImportTool.ImportTool', 'UserFields.UserField'];
	public $components = [
		'CsvView.CsvView', 'Search.Prg', 'AdvancedFilters', 'Pdf', 'Paginator', 'ObjectStatus.ObjectStatus',
		//'Visualisation.Visualisation',
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
				'Api',
				'ApiPagination',
				'BulkActions.BulkActions',
				'Widget.Widget',
				'Visualisation.Visualisation',
				'Taggable.Taggable' => [
					'fields' => ['Tag']
				],
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem',
						'CustomFields.CustomFields',
						'Reports.Reports'
					]
				]
			]
		],
		'UserFields.UserFields' => [
			'fields' => ['Requester']
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

		$this->title = __('Risk Exceptions');
		$this->subTitle = __('Manage all risk exceptions in the scope of this GRP program');
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		$filterConditions = $this->RiskException->parseCriteria($this->Prg->parsedParams());
		if (!empty($filterConditions) && empty($this->request->query['advanced_filter'])) {
			$this->Paginator->settings['conditions'] = $filterConditions;
		}

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->title = __('Risk Exception');
		$this->subTitle = __('Delete a Risk Exception.');

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Risk Exception');
		$this->initAddEditSubtitle();

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit a Risk Exception');
		$this->initAddEditSubtitle();

		return $this->Crud->execute();
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('Risk Exceptions are used to evidence the decision of accepting rather than mitigating a Risk. Risk exceptions are commonly mapped to Risks which the business intends to accept.');
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
