<?php
/**
 * Calendar Library Class.
 */

App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('ClassRegistry', 'Utility');
App::uses('CakeObject', 'Core');
App::uses('AdvancedFiltersHelper', 'AdvancedFilters.View/Helper');
App::uses('FilterAdapter', 'AdvancedFilters.Lib/QueryAdapter');
App::uses('CalendarEvent', 'Dashboard.Lib');
App::uses('Review', 'Model');
App::uses('PolicyException', 'Model');
App::uses('ComplianceAnalysisFinding', 'Model');
App::uses('Project', 'Model');

class DashboardCalendar extends CakeObject
{
	protected $_controller = null;
	protected $_events = [];

	public $reviewModels;
	public $exceptionModels;

	public function __construct()
	{
		$this->reviewModels = [
			'AssetReview' => __('Asset Review'),
			'RiskReview' => __('Risk Review'),
			'ThirdPartyRiskReview' => __('Third Party Risk Review'),
			'BusinessContinuityReview' => __('Business Continuity Review'),
			'SecurityPolicyReview' => __('Security Policy Review')
		];

		$this->exceptionModels = [
			'PolicyException' => __('Policy Exception'),
			'RiskException' => __('Risk Exception'),
			'ComplianceException' => __('Compliance Exception'),
		];

		$this->DashboardCalendarEvent = ClassRegistry::init('Dashboard.DashboardCalendarEvent');

		$this->startup();
		// $this->_controller = $controller;
	}

	public function startup($controller = null) {
		if (!$controller) {
			$controller = new Controller(new CakeRequest());
		}
		$collection = new ComponentCollection();
		$this->controller = $controller;
	}

	public function sync()
	{
		$this->setEvents();

		$events = $this->getEvents();

		$newEvents = [];
		$deletedEvents = [];
		$grouppedEvents = [
			'SecurityServiceAudit' => [],
			'SecurityServiceMaintenance' => [],
			'AssetReview' => [],
			'RiskReview' => [],
			'ThirdPartyRiskReview' => [],
			'BusinessContinuityReview' => [],
			'SecurityPolicyReview' => [],
			'ComplianceException' => [],
			'PolicyException' => [],
			'RiskException' => [],
			'ServiceContract' => [],
			'ComplianceAnalysisFinding' => [],
			'Project' => [],
			'ProjectAchievement' => [],
			'VendorAssessments.VendorAssessmentFinding' => [],
		];

		foreach ($events as $event) {
			$grouppedEvents[$event['model']][] = $event['foreign_key'];

			$Model = ClassRegistry::init($event['model']);

			// first check if given event already exists by certain conditions
			$calendarEvent = $this->DashboardCalendarEvent->find('first', [
				'fields' => [
					'DashboardCalendarEvent.id',
					'DashboardCalendarEvent.title'
				],
				'conditions' => [
					'model' => $event['model'],
					'foreign_key' => $event['foreign_key'],
					'start' => $event['start']
				],
				'recursive' => -1
			]);

			if (empty($calendarEvent)) {
				$newEvents[] = $event;
			}
			elseif (!empty($calendarEvent) && $event['start'] === date('Y-m-d') && !$this->_appNotificationExists($calendarEvent['DashboardCalendarEvent']['id'])) {
				$this->_createAppNotification($calendarEvent['DashboardCalendarEvent']['id'], $calendarEvent['DashboardCalendarEvent']['title']);
			}
		}

		foreach ($grouppedEvents as $model => $foreignKeys) {
			$deleted = $this->DashboardCalendarEvent->find('all', [
				'conditions' => [
					'DashboardCalendarEvent.model' => $model,
					'DashboardCalendarEvent.foreign_key !=' => $foreignKeys 
				],
				'recursive' => -1
			]);

			foreach ($deleted as $del) {
				$deletedEvents[] = $del['DashboardCalendarEvent'];
			}
		}

		$ret = true;
		foreach ($newEvents as $event) {
			$ret &= $this->_saveEvent($event);
		}

		foreach ($deletedEvents as $event) {
			$ret &= $this->_deleteEvent($event);
		}

		// Cache::clearGroup('Visualisation', 'visualisation');
		
		return $ret;
	}

	protected function _saveEvent($event)
	{
		$ret = true;

		// first save the event
		$this->DashboardCalendarEvent->create();
		$this->DashboardCalendarEvent->set($event);
		$ret &= $this->DashboardCalendarEvent->save();

		// then push app notification for new event today
		if ($event['start'] === date('Y-m-d')) {
			// push app notification
			
			$ret &= (bool) $this->_createAppNotification($this->DashboardCalendarEvent->id, $event['title']);
		}

		return $ret;
	}

	protected function _createAppNotification($eventId, $title)
	{
		$ret = $this->DashboardCalendarEvent->createAppNotification('Dashboard.CalendarAppNotification')
			->setTitle($title)
			->setForeignKey($eventId)
			->save();

		return (bool) $ret;
	}

	protected function _appNotificationExists($eventId)
	{
		return (bool) $this->DashboardCalendarEvent->appNotificationExists('Dashboard.CalendarAppNotification', $eventId, true);
	}

	protected function _deleteEvent($event)
	{
		$ret = true;

		// first delete the event
		$ret &= $this->DashboardCalendarEvent->delete($event['id']);

		// delete app notification
		$ret &= (bool) $this->DashboardCalendarEvent->removeAppNotificationsBySubject('Dashboard.CalendarAppNotification', $event['id']);

		return $ret;
	}

	protected function _getConditions($model)
	{
		return null;
		$controller = $this->controller;

		$controller->modelClass = $model;
		$controller->Crud = $controller->Components->load('Crud.Crud');
		$controller->Components->Crud->initialize($controller);
		$controller->Crud->useModel($controller->modelClass);
		$controller->Visualisation = $controller->Components->load('Visualisation.Visualisation');
		$controller->Components->Visualisation->initialize($controller);
		$Model = ClassRegistry::init($controller->modelClass);
		$controller->Crud->addListener('Visualisation', 'Visualisation.Visualisation');
		$Listener = $controller->Crud->listener('Visualisation');

		if ($controller->Visualisation->isEnabled()) {
			$conds = $Listener->getConditions($Model);
			return $conds;
		}

		return null;
	}

	public function addEvent($title, $start, $end, $model, $foreignKey)
	{
		$event = new CalendarEvent();
		$event->title = $title;
		$event->start = $start;
		$event->end = $end;
		$event->model = $model;
		$event->foreignKey = $foreignKey;

		$this->_events[] = $event->buildEvent();
	}

	public function getEvents()
	{
		return $this->_events;
	}

	// get the list of events for calendar
	public function setEvents()
	{
		$this->_getAudits();
		$this->_getMaintenances();
		$this->_getReviews();
		$this->_getExceptions();
		$this->_getSupportContracts();
		$this->_getComplianceAnalysisFindings();
		$this->_getProjects();
		$this->_getProjectTasks();
		$this->_getVendorAssessments();
	}

	protected function _fromAgo()
	{
		return CakeTime::format('Y-m-d', CakeTime::fromString('-2 months'));
	}

	protected function _toDate()
	{
		return CakeTime::format('Y-m-d', CakeTime::fromString('+2 months'));
	}

	public static function getEventTitle($Model)
	{
		if ($Model->alias == 'VendorAssessments.VendorAssessmentFinding') {
			return __('Online Assessments');
		}

		if ($Model->alias == 'ServiceContract') {
			return __('Support Contracts');
		}

		return $Model->label(['displayParents' => true]);
	}

	public function getRecordTitle($Model, $title)
	{
		return sprintf('%s: %s', self::getEventTitle($Model), $title);
	}

	protected function _getAudits()
	{
		$SecurityServiceAudit = ClassRegistry::init('SecurityServiceAudit');
		$data = $SecurityServiceAudit->find('all', [
			'conditions' => array(
				'SecurityServiceAudit.planned_date >=' => $this->_fromAgo(),
				'SecurityServiceAudit.planned_date <=' => $this->_toDate(),
				// 'SecurityServiceAudit.result IS NULL'
			),
			'fields' => array(
				'SecurityServiceAudit.id',
				'SecurityServiceAudit.planned_date',
				'SecurityService.name',
			),
			'recursive' => 0
		]);

		foreach ($data as $item) {
			$title = $this->getRecordTitle($SecurityServiceAudit, $item['SecurityService']['name']);

			$start = $item['SecurityServiceAudit']['planned_date'];


			$this->addEvent($title, $start, null, 'SecurityServiceAudit', $item['SecurityServiceAudit']['id']);
		}
	}

	protected function _getMaintenances()
	{
		$SecurityServiceMaintenance = ClassRegistry::init('SecurityServiceMaintenance');
		$data = $SecurityServiceMaintenance->find('all', [
			'conditions' => array(
				'SecurityServiceMaintenance.planned_date >=' => $this->_fromAgo(),
				'SecurityServiceMaintenance.planned_date <=' => $this->_toDate(),
				// 'SecurityServiceMaintenance.result IS NULL'
			),
			'fields' => array(
				'SecurityServiceMaintenance.id',
				'SecurityServiceMaintenance.planned_date',
				'SecurityService.name',
				// 'COUNT(SecurityServiceMaintenance.id) as count'
			),
			// 'group' => [
			// 	'SecurityServiceMaintenance.planned_date'
			// ],
			'recursive' => 0
		]);

		foreach ($data as $item) {
			$title = $this->getRecordTitle($SecurityServiceMaintenance, $item['SecurityService']['name']);

			$start = $item['SecurityServiceMaintenance']['planned_date'];


			$this->addEvent($title, $start, null, 'SecurityServiceMaintenance', $item['SecurityServiceMaintenance']['id']);
		}
	}

	protected function _getReviews()
	{
		$models = $this->reviewModels;

		foreach ($models as $model => $modelLabel) {
			$Review = ClassRegistry::init($model);
			$parentModel = $Review->parentModel();
			$ParentModel = ClassRegistry::init($parentModel);

			$data = $Review->find('all', [
				'conditions' => array(
					$Review->alias . '.planned_date >=' => $this->_fromAgo(),
					$Review->alias . '.planned_date <=' => $this->_toDate(),
					// $Review->alias . '.completed' => Review::STATUS_NOT_COMPLETE,
				),
				'fields' => array(
					$Review->alias . '.id',
					$Review->alias . '.planned_date',
					$parentModel . '.' . $ParentModel->displayField,
					// 'COUNT(' . $Review->alias . '.id) as count'
				),
				// 'group' => [
				// 	$Review->alias . '.planned_date'
				// ],
				'recursive' => 0
			]);

			foreach ($data as $item) {
				$title = $this->getRecordTitle($Review, $item[$parentModel][$ParentModel->displayField]);

				$start = $item[$Review->alias]['planned_date'];


				$this->addEvent($title, $start, null, $model, $item[$Review->alias]['id']);
			}
		}
	}

	protected function _getExceptions()
	{
		$models = $this->exceptionModels;

		foreach ($models as $model => $modelLabel) {
			$Model = ClassRegistry::init($model);

			$data = $Model->find('all', [
				'conditions' => array(
					$Model->alias . '.expiration >=' => $this->_fromAgo(),
					$Model->alias . '.expiration <=' => $this->_toDate(),
					// $Model->alias . '.status' => PolicyException::STATUS_OPEN,
				),
				'fields' => array(
					$Model->alias . '.id',
					$Model->alias . '.title',
					$Model->alias . '.expiration',
					// 'COUNT(' . $Model->alias . '.id) as count'
				),
				// 'group' => [
				// 	$Model->alias . '.expiration'
				// ],
				'recursive' => -1
			]);

			foreach ($data as $item) {
				$title = $this->getRecordTitle($Model, $item[$Model->alias]['title']);

				$start = $item[$Model->alias]['expiration'];


				$this->addEvent($title, $start, null, $model, $item[$Model->alias]['id']);
			}
		}
	}

	protected function _getSupportContracts()
	{
		$ServiceContract = ClassRegistry::init('ServiceContract');
		$data = $ServiceContract->find('all', [
			'conditions' => array(
				'ServiceContract.start >=' => $this->_fromAgo(),
				'ServiceContract.start <=' => $this->_toDate(),
			),
			'fields' => array(
				'ServiceContract.id',
				'ServiceContract.start',
				'ServiceContract.end',
				'ServiceContract.name'
			),
			'recursive' => -1
		]);

		foreach ($data as $item) {
			// $tmp = [];
			$title = $this->getRecordTitle($ServiceContract, $item['ServiceContract']['name']);
			$start = $item['ServiceContract']['start'];
			$end = $item['ServiceContract']['end'];

			$this->addEvent($title, $start, $end, 'ServiceContract', $item['ServiceContract']['id']);
		}
	}

	protected function _getComplianceAnalysisFindings()
	{
		$ComplianceAnalysisFinding = ClassRegistry::init('ComplianceAnalysisFinding');
		$data = $ComplianceAnalysisFinding->find('all', [
			'conditions' => array(
				'ComplianceAnalysisFinding.due_date >=' => $this->_fromAgo(),
				'ComplianceAnalysisFinding.due_date <=' => $this->_toDate(),
				// 'ComplianceAnalysisFinding.status' => ComplianceAnalysisFinding::STATUS_OPEN,
			),
			'fields' => array(
				'ComplianceAnalysisFinding.id',
				'ComplianceAnalysisFinding.due_date',
				'ComplianceAnalysisFinding.title',
				// 'COUNT(ComplianceAnalysisFinding.id) as count'
			),
			// 'group' => [
			// 	'ComplianceAnalysisFinding.due_date'
			// ],
			'recursive' => -1
		]);

		// ddd($data);

		foreach ($data as $item) {
			$title = $this->getRecordTitle($ComplianceAnalysisFinding, $item['ComplianceAnalysisFinding']['title']);

			$start = $item['ComplianceAnalysisFinding']['due_date'];


			$this->addEvent($title, $start, null, 'ComplianceAnalysisFinding', $item['ComplianceAnalysisFinding']['id']);
		}
	}

	protected function _getProjects()
	{
		$Project = ClassRegistry::init('Project');
		$data = $Project->find('all', [
			'conditions' => array(
				'Project.deadline >=' => $this->_fromAgo(),
				'Project.deadline <=' => $this->_toDate(),
				// 'Project.project_status_id' => Project::STATUS_ONGOING,
			),
			'fields' => array(
				'Project.id',
				'Project.deadline',
				'Project.title',
				// 'COUNT(Project.id) as count'
			),
			// 'group' => [
			// 	'Project.deadline'
			// ],
			'recursive' => -1
		]);

		foreach ($data as $item) {
			$title = $this->getRecordTitle($Project, $item['Project']['title']);

			$start = $item['Project']['deadline'];


			$this->addEvent($title, $start, null, 'Project', $item['Project']['id']);
		}
	}

	protected function _getProjectTasks()
	{
		$ProjectAchievement = ClassRegistry::init('ProjectAchievement');
		$data = $ProjectAchievement->find('all', [
			'conditions' => array(
				'ProjectAchievement.date >=' => $this->_fromAgo(),
				'ProjectAchievement.date <=' => $this->_toDate(),
				// 'Project.project_status_id' => Project::STATUS_ONGOING,
				'ProjectAchievement.completion <' => 100,
			),
			'fields' => array(
				'ProjectAchievement.id',
				'ProjectAchievement.date',
				'Project.title',
				// 'COUNT(ProjectAchievement.id) as count'
			),
			// 'group' => [
			// 	'ProjectAchievement.date'
			// ],
			'recursive' => 0
		]);

		foreach ($data as $item) {
			$title = $this->getRecordTitle($ProjectAchievement, $item['Project']['title']);

			$start = $item['ProjectAchievement']['date'];


			$this->addEvent($title, $start, null, 'ProjectAchievement', $item['ProjectAchievement']['id']);
		}
	}

	protected function _getVendorAssessments()
	{
		if (!AppModule::loaded('VendorAssessments')) {
			return false;
		}

		$VendorAssessmentFinding = ClassRegistry::init('VendorAssessments.VendorAssessmentFinding');
		$data = $VendorAssessmentFinding->find('all', [
			'conditions' => array(
				'VendorAssessmentFinding.deadline >=' => $this->_fromAgo(),
				'VendorAssessmentFinding.deadline <=' => $this->_toDate(),
				// 'VendorAssessmentFinding.status' => VendorAssessmentFinding::STATUS_OPEN,
			),
			'fields' => array(
				'VendorAssessmentFinding.id',
				'VendorAssessmentFinding.deadline',
				'VendorAssessment.title',
				// 'COUNT(VendorAssessmentFinding.id) as count'
			),
			// 'group' => [
			// 	'VendorAssessmentFinding.deadline'
			// ],
			'recursive' => 0
		]);

		foreach ($data as $item) {
			$title = $this->getRecordTitle($VendorAssessmentFinding, $item['VendorAssessment']['title']);

			$start = $item['VendorAssessmentFinding']['deadline'];


			$this->addEvent($title, $start, null, 'VendorAssessments.VendorAssessmentFinding', $item['VendorAssessmentFinding']['id']);
		}
	}

}