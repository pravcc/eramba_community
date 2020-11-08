<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class TeamRolesController extends AppController
{
	public $helpers = [];
	public $components = [
		'Search.Prg', 'Pdf', 'Paginator', 
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
						'CustomFields.CustomFields',
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

        $this->title = __('Program Team Members & Roles');
        $this->subTitle = __('Define program members, roles, teams and their competences. This is of particular relevance for those programs aligned with ISO 27001');
    }

    public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->title = __('Team Roles');
		$this->subTitle = __('Delete a Team Role.');

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Team Role');
		$this->initAddEditSubtitle();

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit a Team Role');
		$this->initAddEditSubtitle();

		return $this->Crud->execute();
	}

	public function trash()
	{
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	private function initAddEditSubtitle() {
		$this->subTitle = false;
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
