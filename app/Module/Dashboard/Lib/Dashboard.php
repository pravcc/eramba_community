<?php
/**
 * Dashboard Library Class.
 */

App::uses('ClassRegistry', 'Utility');
App::uses('CakeObject', 'Core');
App::uses('QueryBuilder', 'AdvancedFilters.Lib');
App::uses('SubQueryFragment', 'AdvancedFilters.Lib/QueryFragment');
App::uses('DebugTimer', 'DebugKit.Lib');
App::uses('DashboardModule', 'Dashboard.Lib');
App::uses('DashboardKpiObject', 'Dashboard.Lib/Dashboard/Kpi');
App::uses('DashboardKpi', 'Dashboard.Model');
App::uses('DashboardException', 'Dashboard.Error');
App::uses('CakeLog', 'Log');
App::uses('ConnectionManager', 'Model');
App::uses('DashboardLog', 'Dashboard.Model');
App::uses('DashboardUserSync', 'Dashboard.Lib');

/**
 * Shell for Dashboard and its nodes sync.
 *
 * @package		Dashboard.Lib
 */
class Dashboard extends CakeObject {

	const DEFAULT_KPI_CLASS_NAME = 'Dashboard.DashboardKpiObject';

	/**
	 * Shell instance for working with shell.
	 * 
	 * @var null|Shell
	 */
	public $Shell = null;

	/**
	 * Model instance for DashboardKpi.
	 * 
	 * @var DashboardKpi
	 */
	public $DashboardKpi = null;

	/**
	 * Loaded attribute classes in the current runtime.
	 * 
	 * @var array
	 */
	protected $_loaded = [];

	/**
	 * List of allowed models that are supposed to be synced automatically.
	 * 
	 * @var array
	 */
	public $allowedModels = [
	];

	/**
	 * List of attribute combinations that will be synced for each Kpi.
	 * 
	 * @var array
	 */
	public $kpiListToSync = [
		// [
		// 	'type' => DashboardKpi::TYPE_USER,
		// 	'category' => DashboardKpi::CATEGORY_GENERAL,
		// 	'className' => 'Visualisation.VisualizedKpi',
		// 	'attributes' => ['User', 'CustomRoles.CustomRole', 'CustomRoles.CustomUser'],
		// 	'syncModel' => null
		// ],
		[
			'type' => DashboardKpi::TYPE_ADMIN,
			'category' => DashboardKpi::CATEGORY_GENERAL,
			'className' => self::DEFAULT_KPI_CLASS_NAME,
			'attributes' => ['Admin'],
			'syncModel' => null
		],
		[
			'type' => DashboardKpi::TYPE_ADMIN,
			'category' => DashboardKpi::CATEGORY_GENERAL,
			'className' => 'CustomQueryKpi',
			'attributes' => ['CustomQuery'],
			'syncModel' => null
		],
		[
			'type' => DashboardKpi::TYPE_ADMIN,
			'category' => DashboardKpi::CATEGORY_RECENT,
			'className' => self::DEFAULT_KPI_CLASS_NAME,
			'attributes' => ['DynamicFilter'],
			'syncModel' => [
				'SecurityService',
				'SecurityPolicy',
				'Risk',
				'BusinessContinuity',
				'Project',
				'SecurityIncident',
				'ThirdPartyRisk'
			]
		],
		[
			'type' => DashboardKpi::TYPE_ADMIN,
			'category' => DashboardKpi::CATEGORY_AWARENESS,
			'className' => 'AwarenessProgramKpi',
			'attributes' => ['AwarenessProgram', 'AwarenessProgramUserModel'],
			'syncModel' => ['AwarenessProgram']
		],
		[
			'type' => DashboardKpi::TYPE_ADMIN,
			'category' => DashboardKpi::CATEGORY_COMPLIANCE,
			'className' => self::DEFAULT_KPI_CLASS_NAME,
			'attributes' => ['ComplianceManagement', 'ComplianceType'],
			'syncModel' => ['ComplianceManagement']
		]
	];

	protected static $countSql = 0;

	public function __construct() {
		$this->DashboardKpi = ClassRegistry::init('Dashboard.DashboardKpi');

		$this->allowedModels = array_unique(array_merge(
			DashboardKpi::listModelsForType(DashboardKpi::TYPE_USER),
			DashboardKpi::listModelsForType(DashboardKpi::TYPE_ADMIN)
		));
	}

	public function out($msg) {
		return $this->Shell->out($msg);
	}

	public function err($msg) {
		return $this->Shell->err($msg);
	}

	/**
	 * Get the result for a calculation of a single KPI object existing in the database.
	 * Takes into consideration all of its Dashboard Attributes to bring and return the final number.
	 * 
	 * @param  string $id Dashboard KPI ID.
	 * @return integer    Result for  specified KPI.
	 * @throws DashboardException When executing the query that consist of attributes fails.
	 */
	public function calculateKpi($id) {
		$data = $this->DashboardKpi->find('first', [
			'conditions' => [
				'DashboardKpi.id' => $id
			]
		]);

		$type = $data['DashboardKpi']['type'];

		$className = $data['DashboardKpi']['class_name'];
		if ($className === null) {
			$className = self::DEFAULT_KPI_CLASS_NAME;
		}

		// handler for a newly refactored user dashboard sync
		if ($className == 'Visualisation.VisualizedKpi' && $type == DashboardKpi::TYPE_USER) {
			if (!isset($this->DashboardUserSync)) {
				$this->DashboardUserSync = new DashboardUserSync();
			}

			return $this->DashboardUserSync->getValue($id);
		}

		list($plugin, $class) = pluginSplit($className, true);

		$className = $class;
		App::uses($class, $plugin . 'Lib/Dashboard/Kpi');

		if (!class_exists($class)) {
			throw new DashboardException(sprintf('Class for Dashboard KPI %s doesnt exist.', $class));
		}

		$DashboardKpiObject = new $className($this, $data);
		$result = $DashboardKpiObject->calculate();

		return $result;
	}

	/**
	 * Synchronize the database to the point it does automatized creation of KPIs based on their attributes.
	 * Attributes are pointers to other features. At last all KPIs are recalculated.
	 * 
	 * @return bool    True on success, False otherwise.
	 */
	public function sync($params = array()) {
		$ret = true;
		
		$this->_logTimer(null);

		$timer1 = 'Dashboard: Structure Synchronization';
		$timer2 = 'Dashboard: Values Recalculation';

		if (isset($params['reset']) && $params['reset']) {
			$ret &= $this->DashboardKpi->deleteAll([true]);
			$timer1 .= ' (with reset)';
		}
		else {
			$timer1 .= ' (without reset)';
		}

		if (isset($params['structure']) && $params['structure']) {
			DebugTimer::start($timer1);
			$models = $this->allowedModels;
			foreach ($models as $model) {
				$ret &= $this->_syncModel($model);
			}
			$this->_logTimer($timer1);
		}

		if (isset($params['values']) && $params['values']) {
			DebugTimer::start($timer2);
			$ret &= $this->syncValues();
			$this->_logTimer($timer2);
		}

		if (!isset($this->DashboardUserSync)) {
			$this->DashboardUserSync = new DashboardUserSync();
		}

		$ret &= $this->DashboardUserSync->sync();

		if (isset($params['logs']) && $params['logs']) {
			// DebugTimer::start($timer2);
			$ret &= $this->storeLogs();
			// $this->_logTimer($timer2);
		}

		return $ret;
	}

	/**
	 * Log details about the dashboard sync process into debug.log.
	 * 
	 * @param  string $name Name of the timer, @see DebugTimer::getAll()
	 * @return boolean		Results of the CakeLog::write() method
	 */
	protected function _logTimer($name = null) {
		$db = ConnectionManager::getDataSource('default');
		$log = $db->getLog();
		$count = $log['count'];
		self::$countSql = $count;

		if ($name !== null) {
			DebugTimer::stop($name);

			$timers = DebugTimer::getAll();
			$took = $timers[$name]['time'];

			return CakeLog::write(LOG_DEBUG, sprintf('%s took %s seconds, during which it made %dx queries on the DB.', $name, $took, self::$countSql));
		}
	}

	/**
	 * Method for syncing dashboard data structure only - no recalculations are done here.
	 * 
	 * @param  string $model Model alias.
	 * @return bool True on success, False otherwise.
	 */
	protected function _syncModel($model) {
		$Model = ClassRegistry::init($model);
		$ret = true;

		$kpiListToSync = $this->kpiListToSync;

		$results = [];
		foreach ($kpiListToSync as $index => $kpi) {
			$syncModel = $kpi['syncModel'];
			// only sync chosen models if defined
			if ($syncModel !== null && !in_array($model, $syncModel)) {
				continue;
			}
			
			$countOfAttributes = count($kpi['attributes']);

			$innerResults = [];
			$normalizedAttributes = Hash::normalize($kpi['attributes']);

			foreach ($normalizedAttributes as $attributeClassName => $whitelist) {
				$Attribute = $this->attributeInstance($attributeClassName);
				if ($whitelist === null) {
					$whitelist = $Attribute->listAttributes($Model);
				}

				$mergableResults = [];
				foreach ($whitelist as $possibleAttribute) {
					$mergableResults[] = [
						$attributeClassName => $possibleAttribute
					];
				}

				$innerResults = $this->mergeAttributes($innerResults, $mergableResults);
			}

			foreach ($innerResults as $attributes) {
				if (count($attributes) !== $countOfAttributes) {
					continue;
				}

				$ret &= $this->saveKpi($Model, $attributes, $kpi);
			}

		}

		return $ret;
	}

	/**
	 * Combine the possible parameters within attributes that will be used for a single KPI.
	 * 
	 * @return array Merged attribute list that can be used for a generic save method in this class.
	 */
	public function mergeAttributes($results, $mergableResults) {
		if (empty($results)) {
			return $mergableResults;
		}

		$final = [];

		foreach ($results as $query) {
			foreach ($mergableResults as $mergeQuery) {
				$final[] = array_merge($query, $mergeQuery);
			}
		}

		return $final;
	}

	/**
	 * Initialize or get the Attribute class that should be included into a KPI among other attributes. 
	 * 
	 * @param  string $object Class name to initialize or if the class is already initialized, get it.
	 * @return DashboardAttribute
	 */
	public function attributeInstance($object, DashboardKpiObject $DashboardKpiObject = null) {
		list($plugin, $class) = pluginSplit($object, true);
		if (isset($this->_loaded[$class])) {
			return $this->_loaded[$class];
		}

		$object = $class;
		$class .= 'DashboardAttribute';
		App::uses($class, $plugin . 'Lib/Dashboard/Attribute');

		if (!class_exists($class)) {
			throw new DashboardException(sprintf('Class for Dashboard attribute %s doesnt exist.', $class));
		}

		$AttributeInstance = new $class($this, $DashboardKpiObject);

		if (!$AttributeInstance instanceof DashboardAttribute) {
			throw new DashboardException(sprintf('Dashboard attribute "%s" must extend DashboardAttribute class.', $class));
		}

		return $this->_loaded[$object] = $AttributeInstance;
	}

	public function resetAttributeInstance($object) {
		list($plugin, $class) = pluginSplit($object, true);
		unset($this->_loaded[$class]);
	}

	/**
	 * Get the query parameters for a single attribute.
	 * 
	 * @param  Model  $Model     Model.
	 * @param  string $className Attribute class name.
	 * @param  string $attribute Attribute value.
	 * @return array             Query parameters for Model->find() operations.
	 */
	public function getQuery(Model $Model, $className, $attribute) {
		return $this->attributeInstance($className)->buildQuery();
	}

	/**
	 * Public method that allows to save a KPI with attributes.
	 * It also checks and skips if there already exist the same one.
	 * 
	 * @param  Model  $Model      Model for which we are trying to save the KPI.
	 * @param  array  $attributes Attributes formatted [0 => ['ClassName' => 'attribute_value'], 1 => ...]
	 * @return bool               True on success, False otherwise.
	 */
	public function saveKpi(Model $Model, $attributes = [], $kpi = []) {
		$DashboardKpiAttribute = [];
		foreach ($attributes as $attributeClass => $attribute) {
			$DashboardKpiAttribute[] = [
				'model' => $attributeClass,
				'foreign_key' => $attribute
			];
		}

		$exist = $this->DashboardKpi->kpiExist($Model->alias, $DashboardKpiAttribute);

		$ret = true;
		if (!$exist) {
			$title = $this->_getKpiTitle($Model, $DashboardKpiAttribute);

			$saveData = [
				'class_name' => empty($kpi['className']) ? null : $kpi['className'],
				'title' => $title,
				'type' => $kpi['type'],
				'category' => $kpi['category'],
				'model' => $Model->alias,
				'DashboardKpiAttribute' => $DashboardKpiAttribute
			];

			$this->DashboardKpi->create($saveData);
			$ret &= $this->DashboardKpi->save();
		}

		// sync the title
		else {
			$data = $this->DashboardKpi->getByAttributes($Model->alias, $DashboardKpiAttribute);
			$title = $this->_getKpiTitle($Model, $DashboardKpiAttribute);

			$id = $data['DashboardKpi']['id'];
			$this->DashboardKpi->id = $id;
			$ret &= (bool) $this->DashboardKpi->saveField('title', $title, false);
		}

		return $ret;
	}

	/**
	 * Build a title for KPI based on provided attributes.
	 */
	protected function _getKpiTitle($Model, $DashboardKpiAttribute)
	{
		$title = [];
		foreach ($DashboardKpiAttribute as $attribute) {
			$Attribute = $this->attributeInstance($attribute['model']);
			$title[] = $Attribute->getLabel($Model, $attribute['foreign_key']);
		}

		$title = array_filter($title);
		$title = implode(', ', $title);

		return $title;
	}

	/**
	 * Method takes each KPI, recalculates it and save the new value.
	 * 
	 * @return bool True on success, False otherwise.
	 */
	public function syncValues() {
		$DashboardKpi = ClassRegistry::init('Dashboard.DashboardKpi');
		$kpi = $DashboardKpi->find('list', [
			'fields' => ['id'],
			'recursive' => -1
		]);

		$ret = true;
		foreach ($kpi as $kpiId) {
			$ret &= $DashboardKpi->recalculate($kpiId);
		}

		$ret &= $this->_saveInternalLog(DashboardLog::TYPE_RECALCULATION);

		return $ret;
	}

	/**
	 * Store value logs about the KPIs.
	 * 
	 * @return bool True on success, False otherwise.
	 */
	public function storeLogs() {
		$ret = true;

		$DashboardKpi = ClassRegistry::init('Dashboard.DashboardKpi');
		$DashboardKpiValueLog = ClassRegistry::init('Dashboard.DashboardKpiValueLog');

		// lets delete all logs that happened more than year ago
		$ret &= $DashboardKpiValueLog->deleteAll([
			'DashboardKpiValueLog.created' => date('Y-m-d', strtotime('-1 year'))
		]);

		$kpi = $DashboardKpi->find('list', [
			'fields' => ['id'],
			'recursive' => -1
		]);

		foreach ($kpi as $kpiId) {
			$ret &= $DashboardKpi->storeLog($kpiId);
		}

		$ret &= $this->_saveInternalLog(DashboardLog::TYPE_STORED_VALUES);

		return $ret;
	}

	/**
	 * Save internal logs about the entire dashboards.
	 */
	protected function _saveInternalLog($type)
	{
		$DashboardLog = ClassRegistry::init('Dashboard.DashboardLog');

		$DashboardLog->create();
		return (bool) $DashboardLog->save([
			'type' => $type
		]);
	}

	/**
	 * Get specified model instance ready for complex filtering.
	 * 
	 * @param  string $model Model.
	 * @return Model
	 */
	public function initModel($model) {
		$Model = ClassRegistry::init($model);
		if (!$Model->Behaviors->loaded('AdvancedFilters.AdvancedFilters')) {
			$Model->Behaviors->load('AdvancedFilters.AdvancedFilters');
		}

		return $Model;
	}

}