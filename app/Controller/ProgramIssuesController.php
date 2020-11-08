<?php
App::uses('AppController', 'Controller');
App::uses('ProgramIssue', 'Model');
App::uses('FormReloadListener', 'Controller/Crud/Listener');

/**
 * @section
 */
class ProgramIssuesController extends AppController
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

	public function beforeFilter()
	{
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'trash', 'history', 'restore']);

		parent::beforeFilter();

		$this->title = __('Program Issues & Challenges');
		$this->subTitle = __('Issues are elements in the internal and/or external environment that drive the development of the program and its goals. This is of particular interest for those programs looking for ISO 27001 compliance.');

		$this->Crud->on('beforeRender', array($this, '_beforeRender'));
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');
		
		return $this->Crud->execute();
	}

	public function _beforeRender(CakeEvent $event) {
		$this->initOptions();
	}

	public function delete($id = null) {
		$this->subTitle =  __('Delete an Issue');

		return $this->Crud->execute();
	}

	public function add() {
		$this->title =  __('Create an Issue');

		$this->Crud->on('beforeSave', [$this, '_beforeSave']);
		$this->Crud->on('beforeRender', [$this, '_addEditBeforeRender']);

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title =  __('Edit an Issue');

		$this->Crud->on('beforeSave', [$this, '_beforeSave']);
		$this->Crud->on('beforeRender', [$this, '_addEditBeforeRender']);
		
		return $this->Crud->execute();
	}

	public function _addEditBeforeRender(CakeEvent $event)
	{
		$data = $event->subject->request->data;

		if ($this->_FieldDataCollection->has('ProgramIssueType')) {
			$typeOptions = ProgramIssue::getInternalTypes();
			if (isset($data['ProgramIssue']['issue_source']) && $data['ProgramIssue']['issue_source'] == ProgramIssue::SOURCE_EXTERNAL) {
				$typeOptions = ProgramIssue::getExternalTypes();
			}
			
			$this->_FieldDataCollection->get('ProgramIssueType')->config('options', $typeOptions);
		}

		if (!empty($data['ProgramIssueType'])) {
			$data['ProgramIssue']['ProgramIssueType'] = $data['ProgramIssueType'];
		}

		if (!empty($data['ProgramIssue']['ProgramIssueType'])) {
			$types = [];
			foreach ($data['ProgramIssue']['ProgramIssueType'] as $item) {
				$types[$item['type']] = $item['type'];
			}
			$event->subject->request->data['ProgramIssue']['ProgramIssueType'] = $types;
		}
	}

	public function _beforeSave(CakeEvent $event)
	{
		if (isset($event->subject->request->data['ProgramIssue']['ProgramIssueType'])) {
			$types = [];

			if (!empty($event->subject->request->data['ProgramIssue']['ProgramIssueType'])) {
				foreach ($event->subject->request->data['ProgramIssue']['ProgramIssueType'] as $item) {
					$types[$item]['type'] = $item;
				}
			}

			$event->subject->request->data['ProgramIssue']['ProgramIssueType'] = $types;
		}
	}

	public function trash()
	{
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	private function initOptions() {
		$this->set('internalTypes', $this->ProgramIssue->getInternalTypes());
		$this->set('externalTypes', $this->ProgramIssue->getExternalTypes());
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
