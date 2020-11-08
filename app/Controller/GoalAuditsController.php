<?php
App::uses('AppController', 'Controller');

class GoalAuditsController extends AppController
{
	public $helpers = [];
	public $components = [
		'Paginator', 'ObjectStatus.ObjectStatus', 
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
						'Reports.Reports',
					]
				]
			]
		],
		'Visualisation.Visualisation',
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

		$this->title = __('Goal Audits');
		$this->subTitle = __('This is a report of all the audits registed for this Goal.');
	}

	public function index($goalId = null) {
		$this->title = __('Goals Performance Review Report');

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Goal Audit');

		return $this->Crud->execute();
	}

	public function add($goalId = null) {
		$this->title = __('Add a Goal Audit');

		$goal = ClassRegistry::init('Goal')->find('first', [
			'conditions' => [
				'Goal.id' => $goalId
			],
			'recursive' => -1
		]);

		// if (empty($goal)) {
		// 	throw new NotFoundException();
		// }

		// $this->request->data['GoalAudit']['goal_id'] = $goal['Goal']['id'];

		if (!empty($goal)) {
			$this->Crud->on('beforeRender', function(CakeEvent $event) use ($goal) {
				if ($event->subject->request->is('get')) {
					$event->subject->request->data['GoalAudit']['audit_metric_description'] = $goal['Goal']['audit_metric'];
					$event->subject->request->data['GoalAudit']['audit_success_criteria'] = $goal['Goal']['audit_criteria'];
				}
			});
		}

		$this->GoalAudit->setCreateValidation();

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit a Goal Audit');

		unset($this->request->data['GoalAudit']['goal_id']);

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

	public function trash() {
		$this->title = __('Goal Audit (Trash)');

		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
    }
}
