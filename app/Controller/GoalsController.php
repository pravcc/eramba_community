<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class GoalsController extends AppController
{    
	public $helpers = [];
	public $components = [
        'Search.Prg', 'Paginator', 'Pdf', 'ObjectStatus.ObjectStatus',
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
				],
				'add' => [
                    'saveMethod' => 'saveAssociated',
                ],
                'edit' => [
                    'saveMethod' => 'saveAssociated',
                ],
				'auditCalendarFormEntry' => [
					'className' => 'AuditCalendarFormEntry',
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

        $this->title = __('Program Goals & Objectives');
        $this->subTitle = __('Define your program goals and objectives. Select all applicable controls, risks and projects that will support this. Define your program metrics and evaluate them at regular periods of time.');
    }

	public function delete($id = null) {
		$this->title = __('Goals');
		$this->subTitle = __('Delete a Goal.');

		return $this->Crud->execute();
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function add() {
        $this->title = __('Create a Goal');

        $this->initAddEditSubtitle();

        $this->initOptions();

        $this->Crud->action()->config('saveAssociatedHandler', false);
		$this->Crud->action()->saveMethod('saveAssociated');
		$this->Crud->action()->saveOptions([
			'deep' => true
		]);
		$this->Crud->on('beforeSave', array($this, '_beforeSave'));

        return $this->Crud->execute();
	}

	public function edit($id = null) {
        $this->title = __('Edit a Goal');

        $this->initAddEditSubtitle();

        $this->initOptions();

        $this->Crud->action()->config('saveAssociatedHandler', false);
		$this->Crud->action()->saveMethod('saveAssociated');
		$this->Crud->action()->saveOptions([
			'deep' => true
		]);
		$this->Crud->on('beforeSave', array($this, '_beforeSave'));

        return $this->Crud->execute();
	}

	public function trash()
	{
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function _beforeSave(CakeEvent $event) {
		$id = null;
		if (isset($event->subject->id)) {
			$id = $event->subject->id;
		}

		if ($id !== null) {
			$this->Goal->GoalAuditDate->deleteAll(array(
				'GoalAuditDate.goal_id' => $id
			));
		}
	}

	private function initOptions() {
		$programIssues = $this->Goal->ProgramIssue->find('list', array(
			'conditions' => array(
				'ProgramIssue.status' => PROGRAM_ISSUE_CURRENT
			),
			'order' => array('ProgramIssue.name' => 'ASC'),
			'recursive' => -1
		));

		$this->set('programIssues', $programIssues);
	}

	private function initAddEditSubtitle() {
		$this->subTitle = false;
	}

	public function auditCalendarFormEntry($model)
	{
		return $this->Crud->execute();
	}

	public function exportPdf($id) {
		$this->autoRender = false;
		$this->layout = 'pdf';

		$item = $this->Goal->find('first', array(
			'conditions' => array(
				'Goal.id' => $id
			),
			'contain' => array(
				'Attachment',
				'Comment' => array('User'),
				'SystemRecord' => array(
					'limit' => 20,
					'order' => array('created' => 'DESC'),
					'User'
				),
				'Owner',
				'SecurityService',
				'Risk',
				'ThirdPartyRisk',
				'BusinessContinuity',
				'Project',
				'SecurityPolicy',
				'ProgramIssue',
				'GoalAudit'
			)
		));

		$vars = array(
			'item' => $item
		);

		$this->set($vars);

		$name = Inflector::slug($item['Goal']['name'], '-');
		$this->Pdf->renderPdf($name, '..'.DS.'Goals'.DS.'export_pdf', 'pdf', $vars, true);
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
