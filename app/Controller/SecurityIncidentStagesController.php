<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class SecurityIncidentStagesController extends AppController
{
	public $name = 'SecurityIncidentStages';
	public $uses = ['SecurityIncidentStage', 'SecurityIncidentStagesSecurityIncident', 'SecurityIncident'];
	public $components = [
		'Paginator', 'Search.Prg', 'ObjectStatus.ObjectStatus',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				],
			],
			'listeners' => [
				'Api', 'ApiPagination', '.SubSection', 'Widget.Widget',
				'.ModuleDispatcher' => [
					'listeners' => [
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

	public function beforeFilter()
	{	
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'history', 'restore']);

		parent::beforeFilter();

		$this->title = __('Security Incident Stages');
		$this->subTitle = __('Describe the lifecycle of a security incident. The stages defined here will be applied to all incidents.');
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function add(){
		$this->title = __('Security Incident Stage');
		$this->subTitle = __('Create a Stage');

		return $this->Crud->execute();
	}

	public function edit($id = null){
		$this->title = __('Security Incident Stage');
		$this->subTitle = __('Edit a Stage');


		return $this->Crud->execute();
	}

	public function delete($id = null){
		$this->title = __('Security Incident Stage');
		$this->subTitle = __('Delete a Stage');

		return $this->Crud->execute();
	}

	private function getItem(){
		$incident = $this->SecurityIncidentStagesSecurityIncident->find('first', array(
			'conditions' => array(
				'id' => $id
			)
		));
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
