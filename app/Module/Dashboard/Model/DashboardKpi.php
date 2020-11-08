<?php
App::uses('DashboardAppModel', 'Dashboard.Model');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('Dashboard', 'Dashboard.Lib');
App::uses('Hash', 'Utility');

class DashboardKpi extends DashboardAppModel {
	public $useTable = 'kpis';

	public $actsAs = [
		'AuditLog.Auditable' => [
			'ignore' => [
				'created', 'modified'
			]
		]
	];

	public $hasOne = [
		'DashboardKpiSingleAttribute' => [
			'className' => 'Dashboard.DashboardKpiAttribute'
		]
	];

	public $hasMany = [
		'DashboardKpiLog' => [
			'className' => 'Dashboard.DashboardKpiLog'
		],
		'DashboardKpiValue' => [
			'className' => 'Dashboard.DashboardKpiValue'
		],
		'DashboardKpiAttribute' => [
			'className' => 'Dashboard.DashboardKpiAttribute'
		],
		'DashboardKpiThreshold' => [
			'className' => 'Dashboard.DashboardKpiThreshold'
		]
	];

	public $validate = array(
		'model' => array(
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field is required',
				'on' => 'create'
			]
		),
		'title' => array(
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => false,
				'allowEmpty' => true,
				'message' => 'This field cannot be left blank'
			]
		),
		'type' => array(
			'callable' => [
				'rule' => ['callbackValidation', ['DashboardKpi', 'types']],
				'message' => 'Incorrect type'
			]
		)
	);

	/**
	 * Instance of a Dashboard class.
	 * 
	 * @var Dashboard|null
	 */
	protected $_Dashboard = null;

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Dashboard KPIs');

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			)
		);

		$this->fieldData = array(
			'title' => array(
				'label' => __('KPI Title'),
				'editable' => true,
				'description' => __('Choose title for your KPI, this will be shown as an additional KPI metric'),
			),
			
		);

		parent::__construct($id, $table, $ds);
	}

	public function beforeSave($options = array()){
		$ret = true;

		$conds = !empty($this->data[$this->alias][$this->primaryKey]);
		$conds &= isset($this->data['DashboardKpiThreshold']);

		if ($conds) {
			$ret &= $this->DashboardKpiThreshold->deleteAll([
				'DashboardKpiThreshold.kpi_id' => $this->data[$this->alias][$this->primaryKey]
			]);
		}

		return $ret;
	}

	public function beforeValidate($options = array()) {
		if (!$this->id) {
			// we set the correct class name to handle KPI calculation if not set
			$cond = !isset($this->data['DashboardKpi']['class_name']);
			$cond = $cond || $this->data['DashboardKpi']['class_name'] === null;
			if ($cond) {
				$this->data['DashboardKpi']['class_name'] = Dashboard::DEFAULT_KPI_CLASS_NAME;
			}

			// build a short form of all attributes for this KPI for easier accessibility
			$this->data['DashboardKpi']['json'] = self::makeJson($this->data['DashboardKpiAttribute']);
		}

		if (!empty($this->data['DashboardKpiAttribute'])) {
			if (!$this->DashboardKpiAttribute->validateMany($this->data['DashboardKpiAttribute'])) {
				// $this->invalidate('attributes', __('Attributes for your KPI are not valid.'));
			}
		}

		return true;
	}

	public function afterSave($created, $options = array()) {
		if ($created) {
			$attrs = Hash::insert($this->data['DashboardKpiAttribute'], '{n}.DashboardKpiAttribute.kpi_id', $this->id);
			$this->DashboardKpiAttribute->saveMany($attrs);

			$this->recalculate($this->id);
		}
	}

	public function kpiExist($model, $attributes) {
		return $this->_findByAttributes('count', $model, $attributes);
	}

	public function getByAttributes($model, $attributes)
	{
		return $this->_findByAttributes('first', $model, $attributes);
	}

	/**
	 * Find method for KPI by attributes JSON.
	 */
	protected function _findByAttributes($type = 'count', $model, $attributes)
	{
		$json = self::makeJson($attributes);
		$data = $this->find($type, [
			'conditions' => [
				'DashboardKpi.model' => $model,
				'DashboardKpi.json' => $json
			],
			'fields' => [
				'DashboardKpi.id',
				'DashboardKpi.title'
			],
			'recursive' => -1
		]);

		return $data;
	}

	public static function makeJson($attributes) {
		$formatAttributes = self::formatAttributes($attributes);
		$json = json_encode($formatAttributes);
		
		return $json;
	}

	public function saveKpi($data, $options = array()) {
		$ret = true;

		$validates = $this->validateAssociated($data, []);
		$data = $this->data;

		if ($validates) {
			return $this->instance()->saveKpi(ClassRegistry::init($data['DashboardKpi']['model']), self::formatAttributes($data['DashboardKpiAttribute']));
		}

		return false;
	}

	/**
	 * Categories for KPI objects.
	 */
	public static function categories($value = null) {
		$options = array(
			self::CATEGORY_GENERAL => __('General'),
			self::CATEGORY_RECENT => __('Recent'),
			self::CATEGORY_STATUS => __('Status'),
			self::CATEGORY_OWNER => __('Customized'),
			self::CATEGORY_AWARENESS => __('Awareness Program Users'),
			self::CATEGORY_COMPLIANCE => __('Compliance Management')
		);
		return parent::enum($value, $options);
	}
	const CATEGORY_GENERAL = 0;
	const CATEGORY_RECENT = 1;
	const CATEGORY_STATUS = 2;
	const CATEGORY_OWNER = 3;
	const CATEGORY_AWARENESS = 4;
	const CATEGORY_COMPLIANCE = 5;


	/**
	 * Types of KPI value.
	 */
	public static function types($value = null) {
		$options = array(
			self::TYPE_USER => __('User'),
			self::TYPE_ADMIN => __('Admin')
		);
		return parent::enum($value, $options);
	}
	const TYPE_USER = 0;
	const TYPE_ADMIN = 1;

	public static function statuses($value = null) {
		$options = array(
			self::STATUS_SYNCED => __('Synced'),
			self::STATUS_NOT_SYNCED => __('Not Synced')
		);
		return parent::enum($value, $options);
	}
	const STATUS_SYNCED = 1;
	const STATUS_NOT_SYNCED = 0;

	public static function formatAttributes($data) {
		return Hash::combine($data, '{n}.model', '{n}.foreign_key');
	}

	public static function listModelsForType($value = null) {
		$options = array(
			self::TYPE_USER => [
				'Risk',
				'BusinessContinuity',
				'ThirdPartyRisk',
				'Project',
				'SecurityService',
				'SecurityPolicy',
				'SecurityIncident',
				'ComplianceAnalysisFinding'
			],
			self::TYPE_ADMIN => [
				'SecurityService',
				'SecurityPolicy',
				'Risk',
				'BusinessContinuity',
				'Project',
				'SecurityIncident',
				'ThirdPartyRisk',

				'AwarenessProgram',
				'ComplianceManagement'
			]
		);
		return parent::enum($value, $options);
	}

	/**
	 * Get the instance of a Dashboard class.
	 * 
	 * @return Dashboard
	 */
	public function instance() {
		if ($this->_Dashboard === null) {
			$this->_Dashboard = new Dashboard();
		}

		return $this->_Dashboard;
	}

	/**
	 * Get a single KPI's data.
	 * 
	 * @param  int $id  KPI ID.
	 * @return array    Array of data.
	 */
	public function getKpi($id) {
		return $this->find('first', [
			'conditions' => [
				'DashboardKpi.id' => $id
			],
			'recursive' => 0
		]);
	}

	/**
	 * Alias method for `Dashboard::sync()` to do a complete database sync.
	 * 
	 * @return bool True on success, False otherwise.
	 */
	public function sync($params = []) {
		return $this->instance()->sync($params);
	}

	/**
	 * Resave all related values for single KPI object.
	 * 
	 * @param  int  $id     Dashboard KPI object ID.
	 * @return bool         True on success, False otherwise.
	 */
	public function recalculate($id) {
		$this->id = $id;
		if (!$this->exists()) {
			return false;
		}

		$ret = true;

		$value = $this->calculate($id);

		$saveData = [
			'id' => $id,
			'value' => $value,
			'status' => self::STATUS_SYNCED
		];

		$ret = $this->save($saveData, false, [
			'value',
			'status'
		]);

		// additionaly save the value in separated model
		$ret &= $this->DashboardKpiValue->recalculate($id);

		return $ret;
	}

	public function storeLog($id)
	{
		$conds = [
			'kpi_id' => $id
		];

		$data = $this->DashboardKpiValue->find('first', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return $this->DashboardKpiValue->DashboardKpiValueLog->saveLog($data['DashboardKpiValue']['id']);
	}

	/**
	 * Calculate and return current value for specified KPI.
	 * 
	 * @param  int $id KPI ID.
	 * @return int     Number value for the KPI.
	 */
	public function calculate($id) {
		return $this->instance()->calculateKpi($id);
	}

	// convert new style formatted array of params into basic query list format
	public static function convertToQuery($params) {
		$list = [];
		foreach ($params as $field => $param) {
			$list[$field] = $param['value'];
			if (isset($param['comparisonType'])) {
				$list[$field . '__comp_type'] = $param['comparisonType'];
			}
		}

		return $list;
	}

	/**
	 * Initialize model for specified KPI and ensure everything is loaded.
	 * 
	 * @param  int $id  KPI ID
	 * @return Model    Instance of a model prepared for executing filter methods.
	 */
	protected function _initKpiModel($id) {
		$data = $this->getKpi($id);

		return $this->instance()->initModel($data['DashboardKpi']['model']);
	}
}
