<?php
App::uses('DashboardAppController', 'Dashboard.Controller');
App::uses('DashboardCalendar', 'Dashboard.Lib');
App::uses('AdvancedFiltersHelper', 'AdvancedFilters.View/Helper');
App::uses('FilterAdapter', 'AdvancedFilters.Lib/QueryAdapter');
App::uses('Review', 'Model');
App::uses('PolicyException', 'Model');
App::uses('ComplianceAnalysisFinding', 'Model');
App::uses('Project', 'Model');

class DashboardCalendarEventsController extends DashboardAppController {

	public $components = [
		'Paginator',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AppIndex',
					'viewVar' => 'data',
					'enabled' => true
					// 'type' => DashboardKpiValue::TYPE_USER
				],
			],
			'listeners' => [
				'Visualisation.Visualisation'
			]
		]
	];

	public function beforeFilter() {
		$this->Crud->on('beforePaginate', array($this, '_beforePaginate'));
		$this->Crud->on('afterPaginate', array($this, '_afterPaginate'));

		parent::beforeFilter();

		$this->Auth->authorize = false;
	}

	public function _beforePaginate(CakeEvent $event) {
		$settings = &$event->subject->paginator->settings;

		$settings['conditions']['DashboardCalendarEvent.start >='] = CakeTime::format('Y-m-d', CakeTime::fromString('-2 months'));
		$settings['conditions']['DashboardCalendarEvent.start <='] = CakeTime::format('Y-m-d', CakeTime::fromString('+2 months'));

		$settings['fields'] = [
			'DashboardCalendarEvent.*',
			'COUNT(DashboardCalendarEvent.id) as count'
		];
		$settings['group'] = [
			'DashboardCalendarEvent.model',
			'DashboardCalendarEvent.start',
		];
		$settings['limit'] = 999;
		$settings['maxLimit'] = 999;
	}

	protected function _getUrl($Model, $start)
	{
		if ($Model->alias == 'SecurityServiceAudit') {
			return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
				'planned_date' => $start,
				// 'result' => FilterAdapter::NULL_VALUE
			], [
				'plugin' => Inflector::underscore($Model->plugin)
			]);
		}

		if ($Model->alias == 'SecurityServiceMaintenance') {
			return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
				'planned_date' => $start,
				// 'result' => FilterAdapter::NULL_VALUE
			], [
				'plugin' => Inflector::underscore($Model->plugin)
			]);
		}

		if ($Model->alias == 'AssetReview') {
			return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
				'planned_date' => $start,
				// 'completed' => Review::STATUS_NOT_COMPLETE
			], [
				'plugin' => Inflector::underscore($Model->plugin)
			]);
		}

		if ($Model->alias == 'RiskReview') {
			return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
				'planned_date' => $start,
				// 'completed' => Review::STATUS_NOT_COMPLETE
			], [
				'plugin' => Inflector::underscore($Model->plugin)
			]);
		}

		if ($Model->alias == 'ThirdPartyRiskReview') {
			return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
				'planned_date' => $start,
				// 'completed' => Review::STATUS_NOT_COMPLETE
			], [
				'plugin' => Inflector::underscore($Model->plugin)
			]);
		}

		if ($Model->alias == 'BusinessContinuityReview') {
			return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
				'planned_date' => $start,
				// 'completed' => Review::STATUS_NOT_COMPLETE
			], [
				'plugin' => Inflector::underscore($Model->plugin)
			]);
		}

		if ($Model->alias == 'SecurityPolicyReview') {
			return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
				'planned_date' => $start,
				// 'completed' => Review::STATUS_NOT_COMPLETE
			], [
				'plugin' => Inflector::underscore($Model->plugin)
			]);
		}

		if ($Model->alias == 'ComplianceException') {
			return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
				'expiration' => $start,
				// 'result' => PolicyException::STATUS_OPEN
			], [
				'plugin' => Inflector::underscore($Model->plugin)
			]);
		}

		if ($Model->alias == 'PolicyException') {
			return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
				'expiration' => $start,
				// 'result' => PolicyException::STATUS_OPEN
			], [
				'plugin' => Inflector::underscore($Model->plugin)
			]);
		}

		if ($Model->alias == 'RiskException') {
			return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
				'expiration' => $start,
				// 'status' => PolicyException::STATUS_OPEN
			], [
				'plugin' => Inflector::underscore($Model->plugin)
			]);
		}

		if ($Model->alias == 'ServiceContract') {
			return false;
			// return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
			// 	'planned_date' => $start,
			// 	'result' => FilterAdapter::NULL_VALUE
			// ], [
			// 	'plugin' => Inflector::underscore($Model->plugin)
			// ]);
		}

		if ($Model->alias == 'ComplianceAnalysisFinding') {
			return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
				'due_date' => $start,
				// 'status' => ComplianceAnalysisFinding::STATUS_OPEN
			], [
				'plugin' => Inflector::underscore($Model->plugin)
			]);
		}

		if ($Model->alias == 'Project') {
			return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
				'deadline' => $start,
				// 'result' => Project::STATUS_ONGOING
			], [
				'plugin' => Inflector::underscore($Model->plugin)
			]);
		}

		if ($Model->alias == 'ProjectAchievement') {
			return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
				'date' => $start,
				// 'project_status_id' => Project::STATUS_ONGOING
			], [
				'plugin' => Inflector::underscore($Model->plugin)
			]);
		}

		if ($Model->alias == 'VendorAssessments.VendorAssessmentFinding') {
			return AdvancedFiltersHelper::filterUrl($Model->getMappedController(), [
				'deadline' => $start,
				// 'status' => VendorAssessmentFinding::STATUS_OPEN
			], [
				'plugin' => Inflector::underscore($Model->plugin)
			]);
		}
	}

	public function _afterPaginate(CakeEvent $event) {
		$events = [];
		foreach ($event->subject->items as $item) {
			$Model = ClassRegistry::init($item['DashboardCalendarEvent']['model']);

			$count = $item[0]['count'];

			if ($count > 1) {
				$title = sprintf('%s (%s)', DashboardCalendar::getEventTitle($Model), $count);
			} else {
				$title = $item['DashboardCalendarEvent']['title'];
			}

			$start = $item['DashboardCalendarEvent']['start'];
			$end = $item['DashboardCalendarEvent']['end'];
			$url = $this->_getUrl($Model, $start);

			$events[] =[
				'title' => $title,
				'start' => $start,
				'end' => $end,
				'url' => $url
			];
		}

		$event->subject->items = $events;
	}

	public function index()
	{
		$this->set( 'title_for_layout', __('Calendar') );
		$this->set( 'subtitle_for_layout', 'This is your calendar' );

		$this->set('calendarLastSyncDate', $this->_getLastCalendarCronTime());

		return $this->Crud->execute();
	}

	protected function _getLastCalendarCronTime()
	{
		App::uses('CronTask', 'Cron');

		$cronTask = ClassRegistry::init('Cron.CronTask')->find('first', [
			'conditions' => [
				'CronTask.task' => 'Dashboard.DashboardCalendarCron',
				'CronTask.status' => CronTask::STATUS_SUCCESS,
			],
			'fields' => ['CronTask.created'],
			'order' => ['CronTask.created' => 'DESC'],
			'recursive' => -1
		]);

		return (!empty($cronTask)) ? $cronTask['CronTask']['created'] : null;
	}
}
