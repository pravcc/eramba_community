<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class ProgramScopesController extends AppController
{
	public $helpers = [];
	public $components = [
		'Search.Prg', 'Paginator', 'Pdf',// 'Visualisation.Visualisation',
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
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem',
						'Reports.Reports',
					]
				]
			]
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

		$this->title = __('GRC Scope');
		$this->subTitle = __('Describe the scope of this GRC program');
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function add()
	{
		$this->title = __('Create a Scope.');

		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function edit( $id = null )
	{
		$this->title = __('Edit a Scope.');

		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function _beforeRender(CakeEvent $event)
	{
		$id = isset($this->request->pass[0]) ? $this->request->pass[0] : null;
		$this->set('hasCurrent', $this->ProgramScope->hasCurrentStatus($id));
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Scope.');

		return $this->Crud->execute();
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
