<?php
App::uses('AppController', 'Controller');
App::uses('SecurityService', 'Model');
App::uses('Hash', 'Utility');

/**
 * @section
 */
class SecurityServicesController extends AppController
{
	public $helpers = ['ImportTool.ImportTool', 'UserFields.UserField'];
	public $components = [
		'Paginator', 'Pdf', 'ObjectStatus.ObjectStatus',
		'Ajax' => [
			'actions' => ['add', 'edit', 'delete'],
			'modules' => ['comments', 'records', 'attachments', 'notifications']
		],
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true,
					'contain' => [
						// 'ObjectStatus'
					]
				],
				'auditCalendarFormEntry' => [
					'className' => 'AuditCalendarFormEntry',
					'enabled' => true
				]
			],
			'listeners' => [
				'Api', 'ApiPagination', 'BulkActions.BulkActions', 'Widget.Widget',
				'Visualisation.Visualisation',
				'Taggable.Taggable' => [
					'fields' => [
						'Classification'
					]
				],
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem',
						'CustomFields.CustomFields',
						'Reports.Reports',
					]
				]
			]
		],
		// 'Visualisation.Visualisation',
		'UserFields.UserFields' => [
			'fields' => ['ServiceOwner', 'Collaborator', 'AuditOwner', 'AuditEvidenceOwner', 'MaintenanceOwner']
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

		$this->Auth->allow('inline_edit', 'trigger_notifications', 'getRecurrenceDatesCount');

		$this->title = __('Internal Controls');
		$this->subTitle = __('Manage all internal controls in the scope of this GRC program.');
	}

	public function index($openId = null) {
		$this->title = __('Internal Controls');

		$this->set('openId', $openId);

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');
		$this->Crud->addListener('ObjectStatus', 'ObjectStatus.ObjectStatus');

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Security Service.');

		return $this->Crud->execute();
	}

	public function add()
	{
		$this->title = __('Create a Security Service');
		$this->initAddEditSubtitle();

		$this->Crud->action()->config('saveAssociatedHandler', false);
		$this->Crud->action()->saveMethod('saveAssociated');
		$this->Crud->action()->saveOptions([
			'deep' => true
		]);
		$this->Crud->on('beforeSave', array($this, '_beforeSave'));
		$this->Crud->on('afterSave', array($this, '_afterSave'));
		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function edit($id = null)
	{
		$id = (int) $id;

		$this->title = __('Edit a Security Service');
		$this->initAddEditSubtitle();

		$this->Crud->action()->config('saveAssociatedHandler', false);
		$this->Crud->action()->saveMethod('saveAssociated');
		$this->Crud->action()->saveOptions([
			'deep' => true
		]);
		$this->Crud->on('beforeSave', array($this, '_beforeSave'));
		$this->Crud->on('afterSave', array($this, '_afterSave'));
		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function getRecurrenceDatesCount($type)
	{
		$this->layout = false;
		$this->autoRender = false;

		$count = 0;
		$name = "";
		$datesExample = "";
		$dates = $this->getAuditsMaintenancesRecurrenceDates();
		if ($type === 'audits' && isset($dates['SecurityServiceAuditDate'])) {
			$count = count($dates['SecurityServiceAuditDate']);
			$name = 'audit';

			for ($i = 0; $i < count($dates['SecurityServiceAuditDate']) && $i < 3; $i++) {
				$d = $dates['SecurityServiceAuditDate'][$i];
				$datesExample .= date('Y', time()) . '-' . $d['month'] . '-' . $d['day'] . ', ';
			}
			$datesExample = substr($datesExample, 0, -2);
		} elseif ($type === 'maintenances' && isset($dates['SecurityServiceMaintenanceDate'])) {
			$count = count($dates['SecurityServiceMaintenanceDate']);
			$name = 'maintenance';

			for ($i = 0; $i < count($dates['SecurityServiceMaintenanceDate']) && $i < 3; $i++) {
				$d = $dates['SecurityServiceMaintenanceDate'][$i];
				$datesExample .= date('Y', time()) . '-' . $d['month'] . '-' . $d['day'] . ', ';
			}
			$datesExample = substr($datesExample, 0, -2);
		}

		if ($datesExample !== "") {
			$datesExample = ' ' . __("Dates") . ': ' . $datesExample . '...';
		}

		$text = __("Based on your settings, we will create %d %s records for this calendar year.%s", $count, $name, $datesExample);

		$this->YoonityJSConnector->setContent($text);
	}

	public function _afterSave(CakeEvent $event) {
		// debug($this->SecurityService->SecurityServiceAudit->validationErrors);
	}

	public function _beforeSave(CakeEvent $event) {
		$id = null;
		if (isset($event->subject->id)) {
			$id = $event->subject->id;
		}

		if ($id !== null) {
			$this->SecurityService->SecurityServiceAuditDate->deleteAll(array(
				'SecurityServiceAuditDate.security_service_id' => $id
			));

			$this->SecurityService->SecurityServiceMaintenanceDate->deleteAll(array(
				'SecurityServiceMaintenanceDate.security_service_id' => $id
			));
		}

		if (isset($this->request->data['SecurityService']['Classification'])
			&& $this->request->data['SecurityService']['Classification'] === ''
		) {
			$this->request->data['SecurityService']['Classification'] = [];
		}
		
		$this->_setAduitsMaintenancesRecurrence();

		$this->_updateFieldsWhenDesign();
		$this->_setAuditsMaintenances($id);
	}

	public function _beforeRender(CakeEvent $event)
	{
		$auditCalendarType = 0;
		if (isset($this->request->data['SecurityService']['audit_calendar_type'])) {
			$auditCalendarType = $this->request->data['SecurityService']['audit_calendar_type'];
		}

		$maintenanceCalendarType = 0;
		if (isset($this->request->data['SecurityService']['maintenance_calendar_type'])) {
			$maintenanceCalendarType = $this->request->data['SecurityService']['maintenance_calendar_type'];
		}
		
		$this->set(compact('auditCalendarType', 'maintenanceCalendarType'));
	}

	protected function _setAduitsMaintenancesRecurrence()
	{
		$dates = $this->getAuditsMaintenancesRecurrenceDates();

		$this->request->data = array_merge($this->request->data, $dates);
	}

	protected function getAuditsMaintenancesRecurrenceDates()
	{
		$requestData = $this->request->data;
		$models = [
			'SecurityServiceAudit' => [
				'calendar_type' => 'SecurityService.audit_calendar_type',
				'start_date' => 'SecurityService.audit_calendar_recurrence_start_date',
				'frequency' => 'SecurityService.audit_calendar_recurrence_frequency',
				'period' => 'SecurityService.audit_calendar_recurrence_period'
			],
			'SecurityServiceMaintenance' => [
				'calendar_type' => 'SecurityService.maintenance_calendar_type',
				'start_date' => 'SecurityService.maintenance_calendar_recurrence_start_date',
				'frequency' => 'SecurityService.maintenance_calendar_recurrence_frequency',
				'period' => 'SecurityService.maintenance_calendar_recurrence_period'
			]
		];

		$dates = [];
		foreach ($models as $model => $fields) {
			$relatedModel = $model . 'Date';
			$calendarType = Hash::get($requestData, $fields['calendar_type'], 0);
			$startDate = Hash::get($requestData, $fields['start_date'], 0);
			$frequency = Hash::get($requestData, $fields['frequency'], 0);
			$period = Hash::get($requestData, $fields['period'], 0);

			if ($calendarType == SecurityService::CALENDAR_TYPE_RECURRENCE_DATE &&
				$startDate != 0 &&
				$frequency != 0 &&
				$period != 0) {
				$newDates = [];
				for ($i = 0; $i < $frequency; ++$i) {
					$startDateUnixTime = strtotime($startDate);
					if ($i != 0) {
						$str = '';
						if ($period == SecurityService::CALENDAR_PERIOD_DAY) {
							$str = '+' . $i . ' days';
						} elseif ($period == SecurityService::CALENDAR_PERIOD_WEEK) {
							$str = '+' . $i . ' weeks';
						} elseif ($period == SecurityService::CALENDAR_PERIOD_MONTH) {
							$str = '+' . $i . ' months';
						} elseif ($period == SecurityService::CALENDAR_PERIOD_QUARTER) {
							$str = '+' . ($i*3) . ' months';
						} elseif ($period == SecurityService::CALENDAR_PERIOD_SEMESTER) {
							$str = '+' . ($i*6) . ' months';
						}

						$startDateUnixTime = strtotime($str, $startDateUnixTime);
					}

					//
					// Make sure date is not weekend
					// if (date('w', $startDateUnixTime) == 0) {
					// 	$startDateUnixTime = strtotime('+1 days', $startDateUnixTime);
					// 	if ($period == SecurityService::CALENDAR_PERIOD_DAY) {
					// 		$i++;
					// 		$frequency++;	
					// 	}
					// } elseif (date('w', $startDateUnixTime) == 6) {
					// 	$startDateUnixTime = strtotime('+2 days', $startDateUnixTime);
					// 	if ($period == SecurityService::CALENDAR_PERIOD_DAY) {
					// 		$i += 2;
					// 		$frequency += 2;
					// 	}
					// }
					// 
					
					$newDate = [
						'day' => date('d', $startDateUnixTime),
						'month' => date('m', $startDateUnixTime),
						'year' => date('Y', $startDateUnixTime)
					];

					if (empty($newDate) || $newDate['year'] != date('Y', time())) {
						break;
					}

					$newDates[] = [
						'day' => $newDate['day'],
						'month' => $newDate['month']
					];
				}

				foreach ($newDates as $date) {
					if (!isset($dates[$relatedModel])) {
						$dates[$relatedModel] = [];
					}

					$dates[$relatedModel][] = $date;
				}
			}
		}

		return $dates;
	}

	protected function _setAuditsMaintenances($id)
	{
		$requestData = $this->request->data;
		$models = [
			'SecurityServiceAudit',
			'SecurityServiceMaintenance'
		];

		foreach ($models as $model) {
			$data = [];

			// i.e SecurityServiceAuditDate
			$dateModel = $model . 'Date';

			// if date model is not part of submitted data, skip
			if (!isset($requestData[$dateModel]) || empty($requestData[$dateModel])) {
				continue;
			}

			// cycle all dates for a date model
			foreach ($requestData[$dateModel] as $date) {
				$dateFormat = 'Y-m-d';
				$formattedDate = date_create_from_format($dateFormat, date('Y') . '-' . intval($date['month']) . '-' . intval($date['day']));
				$formattedDate = date_format($formattedDate, $dateFormat);

				$_data = [
					'planned_date' => $formattedDate
				];

				// configure correct data
				if ($model == 'SecurityServiceAudit') {
					$_data = $_data + [
						'AuditOwner' => $requestData['SecurityService']['AuditOwner'],
						'AuditEvidenceOwner' => $requestData['SecurityService']['AuditEvidenceOwner'],
						'audit_metric_description' => $requestData['SecurityService']['audit_metric_description'],
						'audit_success_criteria' => $requestData['SecurityService']['audit_success_criteria'],
					];
				}

				if ($model == 'SecurityServiceMaintenance') {
					$_data = $_data + [
						'MaintenanceOwner' => $requestData['SecurityService']['MaintenanceOwner'],
						'task' => $requestData['SecurityService']['maintenance_metric_description']
					];
				}

				$dataItem = [
					$model => $_data
				];

				if ($id !== null) {
					// check for the same audit/maintenance to no create duplicates
					$secServData = $this->SecurityService->{$model}->find('all', array(
						'fields' => array(
							$model . '.id',
							$model . '.planned_date'
						),
						'conditions' => array(
							$model . '.security_service_id' => $id,
							$model . '.planned_date' => $formattedDate
						),
						'recursive' => -1
					));

					if (!empty($secServData)) {
						continue;
					}
				}

				$data[] = $dataItem;
			}

			$this->request->data[$model] = $data;
		}
	}

	public function trash() {
	    $this->set( 'title_for_layout', __( 'Internal Controls (Trash)' ) );
	    $this->set( 'subtitle_for_layout', __( 'This is the list of internal controls.' ) );

	    $this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

	    return $this->Crud->execute();
	}

	protected function _updateFieldsWhenDesign()
	{
		//only on form submit
		if (!$this->request->is('post') && !$this->request->is('put')) {
			return;
		}

		$auditsText = __('The control is in design. Audits not possible.');
		$maintenancesText = __('The control is in design. Maintenances not possible.');

		if (isset($this->request->data['SecurityService']['security_service_type_id'])
			&& $this->request->data['SecurityService']['security_service_type_id'] == SECURITY_SERVICE_DESIGN
		) {
			$text = Hash::get($this->request->data, 'SecurityService.audit_metric_description', false);
			if ($text !== false) {
				$pos = strpos($text, $auditsText);

				if ($pos === false) {
					$this->request->data['SecurityService']['audit_metric_description'] .= ' ' . $auditsText;
				}
			}

			$text = Hash::get($this->request->data, 'SecurityService.audit_success_criteria', false);
			if ($text !== false) {
				$pos = strpos($text, $auditsText);

				if ($pos === false) {
					$this->request->data['SecurityService']['audit_success_criteria'] .= ' ' . $auditsText;
				}
			}

			$text = Hash::get($this->request->data, 'SecurityService.maintenance_metric_description', false);
			if ($text !== false) {
				$pos = strpos($text, $maintenancesText);

				if ($pos === false) {
					$this->request->data['SecurityService']['maintenance_metric_description'] .= ' ' . $maintenancesText;
				}
			}
		}
	}

	public function auditCalendarFormEntry($model)
	{
		return $this->Crud->execute();	
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('');
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
