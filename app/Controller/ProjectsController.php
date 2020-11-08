<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class ProjectsController extends AppController
{
	public $helpers = ['ImportTool.ImportTool', 'UserFields.UserField'];
	public $components = [
		'Search.Prg', 'AdvancedFilters', 'Paginator', 'Pdf', 'Paginator', 'ObjectStatus.ObjectStatus',
		//'Visualisation.Visualisation',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
			],
			'listeners' => [
				'Api', 'ApiPagination', 'BulkActions.BulkActions', 'Widget.Widget',
				'Taggable.Taggable' => [
					'fields' => ['Tag']
				],
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
			'fields' => ['Owner']
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

		$this->title = __('Projects');
		$this->subTitle = __('This module will let you define and manage improvements across your program. Once defined here, projects can then be mapped to Compliance, Risk, Controls and Exceptions in order to clearly see what their impact is on the program.');
	}

	// public function _beforePaginate(CakeEvent $event)
	// {
	// 	$event->subject->paginator->settings['contain'] = Hash::merge([
	// 		'ProjectAchievement' => $this->UserFields->attachFieldsToArray('TaskOwner', [
	// 			'fields' => ['id', 'description', 'date', 'completion', 'task_order', 'task_duration'],
	// 			'Comment',
	// 			'Attachment',
	// 			'order' => ['ProjectAchievement.task_order' => 'ASC'],
	// 			'NotificationObject'
	// 		], 'ProjectAchievement')
	// 	], $event->subject->paginator->settings['contain']);
	// }

	// public function _afterPaginate(CakeEvent $event) {
	// 	$event->subject->items = $this->addCompletion($event->subject->items);
	// }

	public function index() {
		$this->title = __('Project Management');

		// $this->Crud->on('beforePaginate', array($this, '_beforePaginate'));
		// $this->Crud->on('afterPaginate', array($this, '_afterPaginate'));

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	// private function addCompletion($data) {
	// 	if (!empty($data)) {
	// 		foreach ($data as $key => $item) {
	// 			$data[$key]['Project']['ultimate_completion'] = $this->Project->getUltimateCompletion($item['Project']['id']);
	// 		}
	// 	}

	// 	return $data;
	// }

	private function getProjectStatuses() {
		$statuses = $this->Project->ProjectStatus->find( 'list', array(
			'order' => array('ProjectStatus.name' => 'ASC'),
			'recursive' => -1
		) );

		return $statuses;
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Project.');

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Project');
		$this->initAddEditSubtitle();

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit a Project');
		$this->initAddEditSubtitle();

		return $this->Crud->execute();
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('This module will let you define and manage improvements across your program. Once defined here, projects can then be mapped to Compliance, Risk, Controls and Exceptions in order to clearly see what their impact is on the program.');
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
