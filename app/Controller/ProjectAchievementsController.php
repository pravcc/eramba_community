<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class ProjectAchievementsController extends AppController
{
	public $helpers = ['UserFields.UserField'];
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
		'UserFields.UserFields' => [
			'fields' => ['TaskOwner']
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

		$this->title = __('Project Tasks');
		$this->subTitle = __('This is the list of tasks for a given project.');
	}

	public function index($project_id = null) {
		$this->title = __('List of Project Tasks');

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Project Task.');

		return $this->Crud->execute();
	}

	public function trash()
	{
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');
		
		return $this->Crud->execute();
	}

	public function add($projectId = null)
	{
		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function edit($id = null)
	{
		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function _beforeRender(CakeEvent $event)
	{
		$request = $event->subject->request;

		$projectId = null;
		$achievementId = null;
		if ($request->params['action'] === 'add') {
			if (isset($request->data['ProjectAchievement']['project_id'])) {
				$projectId = $request->data['ProjectAchievement']['project_id'];
			}
		}

		if (!empty($event->subject->item)) {
			$data = $event->subject->item;
			$achievementId = $event->subject->id;
		}

		$this->initOptions($projectId, $achievementId);
	}

	/**
	 * Initialize options for join elements.
	 */
	private function initOptions($project_id = null, $achievement_id = null) {
		$conds = array();
		if ($project_id) {
			$conds['ProjectAchievement.project_id'] = $project_id;
		}
		if ($achievement_id) {
			$conds['ProjectAchievement.id !='] = $achievement_id;
		}

		$lastAchievement = $lastOrder = false;
		// when on /add, autocomplete for the date and order field, with the latest available information
		if ($achievement_id === null) {
			$lastAchievement = $this->ProjectAchievement->field(
				'date',
				['ProjectAchievement.project_id' => $project_id],
				'ProjectAchievement.date DESC'
			);

			$lastOrder = $this->ProjectAchievement->field(
				'task_order',
				['ProjectAchievement.project_id' => $project_id],
				'ProjectAchievement.task_order DESC'
			);
		}
		
		$this->set('lastAchievement', $lastAchievement);
		$this->set('lastOrder', $lastOrder);

		$order = array();
		for ($i = 100; $i >= 1; $i--) {
			$order[$i] = $i;
		}

		$this->set('order', $order);
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('Define and update tasks involved on this project.');
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
