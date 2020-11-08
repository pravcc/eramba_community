<?php
App::uses('DashboardAppController', 'Dashboard.Controller');
App::uses('DashboardKpiValue', 'Dashboard.Model');
App::uses('Hash', 'Utility');
App::uses('DashboardKpi', 'Dashboard.Model');
App::uses('Dashboard', 'Dashboard.Lib');

class DashboardKpisController extends DashboardAppController {
	public $helpers = ['Dashboard.Dashboard', 'FieldData.FieldData'];

	public $components = [
		'Paginator',
		'Ajax' => [
			'actions' => ['add', 'edit', 'delete'],
		],
		'Crud.Crud' => [
			'actions' => [
				'user' => [
					'className' => 'AppIndex',
					'viewVar' => 'data',
					'type' => DashboardKpiValue::TYPE_USER
				],
				'admin' => [
					'className' => 'AppIndex',
					'viewVar' => 'data',
					'type' => DashboardKpiValue::TYPE_ADMIN
				],

				'add' => [
					'enabled' => true,
					'className' => 'AppAdd',
					'view' => 'add',
				],
				'edit' => [
					'enabled' => true,
					'className' => 'AppEdit',
					'view' => 'add'
				],
				'delete' => [
					'enabled' => true,
					'className' => 'AppDelete',
				]
			]
		]
	];

	public function beforeFilter() {
		$this->Crud->on('beforePaginate', array($this, '_beforePaginate'));
		$this->Crud->on('afterPaginate', array($this, '_afterPaginate'));
		
		parent::beforeFilter();
		$this->Auth->authorize = false;

		$this->adminConditions = [
			'DashboardKpi.type' => DashboardKpi::TYPE_ADMIN,
			'DashboardKpi.category' => [
				DashboardKpi::CATEGORY_GENERAL,
				DashboardKpi::CATEGORY_RECENT,
				DashboardKpi::CATEGORY_OWNER,
				DashboardKpi::CATEGORY_AWARENESS,
				DashboardKpi::CATEGORY_COMPLIANCE,
			],
			'DashboardKpi.model' => DashboardKpi::listModelsForType(DashboardKpi::TYPE_ADMIN)
		];
	}

	/**
	 * Initialize Dashboard class instance.
	 * 
	 * @return Dashboard
	 */
	protected function _initDashboards()
	{
		$DashboardKpi = ClassRegistry::init('Dashboard.DashboardKpi');
		$Dashboard = $DashboardKpi->instance();

		return $Dashboard;
	}

	/**
	 * Flashes an error and redirects to user page.
	 */
	protected function _denyAdminDashboard()
	{
		$this->Flash->error(__('You are not allowed to view admin dashboard as you are not in admin group.'));
		return $this->redirect(['plugin' => 'dashboard', 'controller' => 'dashboardKpis', 'action' => 'user']);
	}

	public function store_logs() {
		if (!isAdmin($this->logged)) {
			return $this->_denyAdminDashboard();
		}

		$params = [
			'structure' => true,
			'logs' => true
		];

		$ret = $this->_initDashboards()->sync($params);

		if ($ret) {
			$this->Flash->success(__('Dashboard logs have been successfully stored.'));
		}
		else {
			$this->Flash->error(__('Error occured, please try again.'));
		}

		return $this->redirect(['plugin' => 'dashboard', 'controller' => 'dashboardKpis', 'action' => 'admin']);
	}

	public function recalculate_values() {
		if (!isAdmin($this->logged)) {
			return $this->_denyAdminDashboard();
		}
		
		$params = [
			'structure' => true,
			'values' => true
		];

		$ret = $this->_initDashboards()->sync($params);

		if ($ret) {
			$this->Flash->success(__('Dashboard values have been successfully recalculated.'));
		}
		else {
			$this->Flash->error(__('Error occured, please try again.'));
		}

		return $this->redirect(['plugin' => 'dashboard', 'controller' => 'dashboardKpis', 'action' => 'admin']);
	}

	/**
	 * Export current values as CSV.
	 * 
	 * @return void
	 */
	public function export_values()
	{
		$data = $this->DashboardKpi->find('all', [
			'conditions' => $this->adminConditions,
			'recursive' => -1
		]);

		foreach ($data as &$item) {
			$item['DashboardKpi']['title_with_section'] = $this->_titleWithSection($item);
		}

		$_serialize = 'data';
		$_header = [
			'KPI',
			'Value'
		];
		$_extract = [
			'DashboardKpi.title_with_section',
			'DashboardKpi.value'
		];

		$_bom = true;

		$this->response->download('dashboard_current_values.csv');
		$this->viewClass = 'CsvView.Csv';
		$this->set(compact('data', '_serialize', '_header', '_extract', '_newline', '_bom'));
	}

	/**
	 * Export all values stored in logs as CSV.
	 * 
	 * @return void
	 */
	public function export_logs()
	{
		$DashboardKpiValueLog = $this->DashboardKpi->DashboardKpiValue->DashboardKpiValueLog;
		$data = $DashboardKpiValueLog->find('all', [
			'conditions' => $this->adminConditions,
			'joins' => [
				[
					'table' => 'dashboard_kpis',
					'alias' => 'DashboardKpi',
					'type' => 'LEFT',
					'conditions' => [
						'DashboardKpi.id = DashboardKpiValueLog.kpi_id'
					]

				]
			],
			'fields' => [
				'DashboardKpi.id',
				'DashboardKpi.title',
				'DashboardKpi.model',
				'DashboardKpiValueLog.created',
				'DashboardKpiValueLog.value',
				'DATE(DashboardKpiValueLog.created) AS date'
			],
			'order' => [
				'DashboardKpi.id' => 'ASC',
				'date' => 'ASC'
			],
			'group' => [
				'DashboardKpi.id',
				'DashboardKpiValueLog.created',
				'DATE(DashboardKpiValueLog.created)'
			],
			'recursive' => -1
		]);

		foreach ($data as &$item) {
			$item['DashboardKpi']['title_with_section'] = $this->_titleWithSection($item);
		}

		$_serialize = 'data';
		$_header = [
			'KPI',
			'Date',
			'Value'
		];
		$_extract = [
			'DashboardKpi.title_with_section',
			'0.date',
			'DashboardKpiValueLog.value'
		];

		$_bom = true;

		$this->response->download('dashboard_historical_values.csv');
		$this->viewClass = 'CsvView.Csv';
		$this->set(compact('data', '_serialize', '_header', '_extract', '_newline', '_bom'));
	}

	protected function _titleWithSection($item)
	{
		$sectionLabel = ClassRegistry::init($item['DashboardKpi']['model'])->label();
		$kpiTitle = $item['DashboardKpi']['title'];

		return sprintf('%s - %s', $sectionLabel, $kpiTitle);
	}

	public function _beforePaginate(CakeEvent $event) {
		$event->subject->paginator->settings['conditions']['DashboardKpi.owner_id'] = [
			null,
			$event->subject->controller->logged['id']
		];

		$event->subject->paginator->settings['recursive'] = 0;
		$event->subject->paginator->settings['contain'] = [
			'DashboardKpiAttribute',
			'DashboardKpiSingleAttribute'
		];

		$event->subject->paginator->settings['group'] = ['DashboardKpi.id'];
		$event->subject->paginator->settings['limit'] = 999;
		$event->subject->paginator->settings['maxLimit'] = 999;
	}

	public function _afterPaginate(CakeEvent $event) {
		$items = [];
		foreach ($event->subject->items as $item) {
			$item['attributes'] = DashboardKpi::formatAttributes($item['DashboardKpiAttribute']);

			$assignKpiToModel = $item['DashboardKpi']['model'];
			$items[$assignKpiToModel][$item['DashboardKpi']['category']][] = $item;
		}

		$event->subject->items = $items;
	}

	public function user() {
		$this->Crud->on('beforePaginate', array($this, '_beforePaginateUser'));
		$this->Crud->on('beforePaginate', array($this, '_beforePaginateValidation'));

		$this->set('title_for_layout', __('User KPI Dashboard'));
		$this->set('subtitle_for_layout', __('Shows a summary for all major KPIs on the system that apply to your user account'));

		$AttributeInstance = $this->DashboardKpi->instance()->attributeInstance('User');
		$this->set('AttributeInstance', $AttributeInstance);

		$this->Crud->execute();
	}

	public function _beforePaginateUser(CakeEvent $event) {
		$event->subject->paginator->settings['joins'] = [
			[
				'table' => 'dashboard_kpi_attributes',
				'alias' => 'DashboardKpiAttribute',
				'type' => 'INNER',
				'conditions' => [
					'DashboardKpiAttribute.kpi_id = DashboardKpi.id'
				]

			]
		];
		$event->subject->paginator->settings['conditions'] = [
			'DashboardKpi.type' => DashboardKpi::TYPE_USER,
			'DashboardKpi.category' => [
				DashboardKpi::CATEGORY_GENERAL,
				DashboardKpi::CATEGORY_OWNER
			],
			'DashboardKpi.model' => DashboardKpi::listModelsForType(DashboardKpi::TYPE_USER),
			'OR' => [
				[
					"DashboardKpiAttribute.model = 'CustomRoles.CustomUser'",
					'DashboardKpiAttribute.foreign_key' => $event->subject->controller->logged['id']
				],
				[
					'DashboardKpi.owner_id' => $event->subject->controller->logged['id'],
					"DashboardKpiAttribute.model = 'AdvancedFilter'"
				]
			]
		];
	}

	public function _beforePaginateValidation(CakeEvent $event) {
		$settings = $event->subject->paginator->settings;

		$DashboardKpi = ClassRegistry::init('Dashboard.DashboardKpi');
		$dashboardReady = (boolean) $DashboardKpi->find('count', $settings);

		$settings['conditions']['DashboardKpi.status'] = DashboardKpi::STATUS_NOT_SYNCED; 
		$dashboardReady &= (boolean) !$DashboardKpi->find('count', $settings);
		$this->set('dashboardReady', $dashboardReady);
	}

	public function admin() {
		if (!isAdmin($this->logged)) {
			return $this->_denyAdminDashboard();
		}

		$this->Crud->on('beforeRender', array($this, '_beforeRenderAdmin'));
		$this->Crud->on('beforePaginate', array($this, '_beforePaginateAdmin'));
		$this->Crud->on('beforePaginate', array($this, '_beforePaginateValidation'));
		$this->Crud->on('afterPaginate', array($this, '_afterPaginateAdmin'), [
			// priority higher than the one in _beforeFilter() so its easier to maintain
			'priority' => 5
		]);

		$AwarenessUserInstance = $this->DashboardKpi->instance()->attributeInstance('AwarenessProgramUserModel');
		$AwarenessInstance = $this->DashboardKpi->instance()->attributeInstance('AwarenessProgram');
		$this->set('AwarenessUserInstance', $AwarenessUserInstance);
		$this->set('AwarenessInstance', $AwarenessInstance);

		$AwarenessProgram = ClassRegistry::init('AwarenessProgram');
		$awarenessAttributes = $AwarenessInstance->listAttributes($AwarenessProgram);
		$awarenessPrograms = $AwarenessProgram->find('list', [
			'conditions' => [
				'AwarenessProgram.id' => $awarenessAttributes
			],
			'recursive' => -1
		]);
		$this->set('awarenessPrograms', $awarenessPrograms);

		$ComplianceTypeInstance = $this->DashboardKpi->instance()->attributeInstance('ComplianceType');
		$ComplianceInstance = $this->DashboardKpi->instance()->attributeInstance('ComplianceManagement');
		$this->set('ComplianceTypeInstance', $ComplianceTypeInstance);
		$this->set('ComplianceInstance', $ComplianceInstance);

		$CompliancePackageRegulator = ClassRegistry::init('CompliancePackageRegulator');
		$complianceAttributes = $ComplianceInstance->listAttributes($CompliancePackageRegulator);

		$compliancePackageRegulators = $CompliancePackageRegulator->find('list', [
			'conditions' => [
				'CompliancePackageRegulator.id' => $complianceAttributes
			],
			'recursive' => -1
		]);
		$this->set('compliancePackageRegulators', $compliancePackageRegulators);

		$this->set('title_for_layout', __('General KPI Dashboard'));
		$this->set('subtitle_for_layout', __('Shows a summary for all major KPIs on the system'));

		$this->Crud->addListener('Dashboard', 'Dashboard.Dashboard');

		$this->Crud->execute();
	}

	public function _beforeRenderAdmin(CakeEvent $e)
	{
		$DashboardLog = ClassRegistry::init('Dashboard.DashboardLog');

		$recalculationEvent = $DashboardLog->getLastEvent(DashboardLog::TYPE_RECALCULATION);
		$storedValuesEvent = $DashboardLog->getLastEvent(DashboardLog::TYPE_STORED_VALUES);
		
		$e->subject->controller->set(compact('recalculationEvent', 'storedValuesEvent'));
	}

	public function _beforePaginateAdmin(CakeEvent $event) {
		$settings = &$event->subject->paginator->settings;
		$settings['conditions'] = $this->adminConditions;

		$settings['contain']['DashboardKpiThreshold'] = [
			'order' => [
				'DashboardKpiThreshold.percentage' => 'DESC',
				'DashboardKpiThreshold.id' => 'ASC'
			]
		];
	}

	/**
	 * Search for previous values of each kpi to correctly show (percentage) thresholds
	 */
	public function _afterPaginateAdmin(CakeEvent $event) {

		$primaryIds = Hash::extract($event->subject->items, '{n}.DashboardKpi.id');

		// get the latest previous values for a specific set of KPI IDs
		$DashboardKpiValueLog = ClassRegistry::init('Dashboard.DashboardKpiValueLog');
		$dataObject = $DashboardKpiValueLog->advancedFind('all', [
			'conditions' => [
				'DashboardKpiValueLog.kpi_id' => $primaryIds,
				'DashboardKpiValueLog.id' => $DashboardKpiValueLog->advancedFind('all', [
					'fields' => ['MAX(DashboardKpiValueLog.id)'],
					'group' => ['DashboardKpiValueLog.kpi_id'],
					'contain' => []
				])
			],
			'contain' => []
		]);

		$data = $dataObject->get();
		$previousValues = Hash::combine($data, '{n}.DashboardKpiValueLog.kpi_id', '{n}.DashboardKpiValueLog.value');

		foreach ($event->subject->items as &$item) {
			if (isset($previousValues[$item['DashboardKpi']['id']])) {
				$item['DashboardKpiLastLog']['value'] = $previousValues[$item['DashboardKpi']['id']];
			}
		}
	}

	public function add($type = 1) {
		$this->Crud->on('beforeRender', array($this, '_initOptions'));
		$this->Crud->on('beforeSave', array($this, '_beforeSave'));
		$this->Crud->on('afterSave', array($this, '_afterSave'));

		$this->set('title_for_layout', __('Add a new one'));
		$this->set('subtitle_for_layout', __('Summary for all sections.'));
		
		return $this->Crud->execute();
	}



	public function edit($id) {
		$this->Crud->on('beforeRender', array($this, '_initOptions'));
		$this->Crud->on('beforeSave', array($this, '_beforeSave'));
		$this->Crud->on('afterSave', array($this, '_afterSave'));

		$this->set('title_for_layout', __('Edit existing KPI'));
		$this->set('subtitle_for_layout', __('Summary for all sections.'));
		
		return $this->Crud->execute();
	}

	/**
	 * Handler for adding a new threshold item.
	 * 
	 * @param  integer $index Index for hasMany relation.
	 * @return void
	 */
	public function thresholdItem($index = 0) {
		$this->YoonityJSConnector->deny();

		$DashboardKpiThreshold = $this->DashboardKpi->DashboardKpiThreshold;
		$DashboardKpiThresholdCollection = $DashboardKpiThreshold->getFieldCollection();
		
		// $this->_setDynamicClassificationFields($this->request->query['types']);
		$this->set($DashboardKpiThresholdCollection->getViewOptions('DashboardKpiThresholdCollection'));
		// $this->set('DashboardKpiThresholdCollection', $DashboardKpiThresholdCollection);
		$this->set('index', $index);

		$this->render('Dashboard.../Elements/threshold_item');
	}

	public function delete() {
		$this->set('title_for_layout', __('Delete your KPI'));

		return $this->Crud->execute();
	}

	public function _initOptions(CakeEvent $event) {
		$subject = $event->subject;
		$controller = $subject->controller;
		$model = $subject->model;

		//set type based on an action
		$param = $event->subject->request->params['pass'];
		$action = $event->subject->request->params['action'];
		if ($action == 'add') {
			$type = DashboardKpi::TYPE_ADMIN;
			$category = DashboardKpi::CATEGORY_OWNER;
		}
		elseif ($action == 'edit') {
			$id = $param[0];
			$kpi = $event->subject->model->find('first', [
				'conditions' => [
					'DashboardKpi.id' => $id
				],
				'fields' => [
					'DashboardKpi.type',
					'DashboardKpi.category'
				],
				'recursive' => -1
			]);

			$type = $kpi['DashboardKpi']['type'];
			$category = $kpi['DashboardKpi']['category'];
		}
		$controller->set('dashboardKpiType', $type);
		$controller->set('dashboardKpiCategory', $category);

		$controller->set($model->getFieldCollection()->getViewOptions());
		$Attribute = $model->DashboardKpiSingleAttribute;

		$controller->set('DashboardKpiSingleAttribute', $Attribute);
		$controller->set($Attribute->getFieldCollection()->getViewOptions('Attribute'));

		$DashboardKpiThresholdCollection = $this->DashboardKpi->DashboardKpiThreshold->getFieldCollection();
		$thresholdViewOptions = $DashboardKpiThresholdCollection->getViewOptions('DashboardKpiThresholdCollection');
		$this->set($thresholdViewOptions);
	}

	public function _beforeSave(CakeEvent $event) {
		$data = &$event->subject->request->data;

		if ($data['DashboardKpi']['category'] == DashboardKpi::CATEGORY_OWNER) {
			$attrClass = $data['DashboardKpiAttribute'][0]['model'];
			$filterId = $data['DashboardKpiAttribute'][0]['foreign_key'];

			$AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
			$AdvancedFilter->id = $filterId;

			$data['DashboardKpi']['model'] = $AdvancedFilter->field('model');
			$data['DashboardKpi']['owner_id'] = $event->subject->controller->logged['id'];

			// we set the title as required field
			$Model = $event->subject->model;
			$titleField = $Model->validator()->getField('title');
			$titleRule = $titleField->getRule('notBlank');
			$titleRule->allowEmpty = false;
		}

		// reset the thresholds
		if (!isset($data['DashboardKpiThreshold'])) {
			$data['DashboardKpiThreshold'] = [];
		}
	}

	public function _afterSave(CakeEvent $event) {
		$this->DashboardKpi->recalculate($event->subject->id);
	}

	public function sync($reset = false) {
		dd($this->DashboardKpi->sync(compact('reset')));
	}

}
