<?php
App::uses('Hash', 'Utility');
App::uses('FilterAdapter', 'AdvancedFilters.Lib/QueryAdapter');

class FilterField {
	const DEFAULT_ORDER = 10;
	/**
	 * Allowed keys for field filter preferences.
	 * 
	 * @var array
	 */
	public static $allowedPreferences = ['comparisonType', 'show'];

	/**
	 * Basic field name.
	 * 
	 * @var string
	 */
	protected $_field = null;

	protected $_defaults = [];

	/**
	 * Config.
	 * 
	 * @var array
	 */
	protected $_config = [];

	/**
	 * Reference to a Model instance.
	 * 
	 * @var Model
	 */
	protected $_model = null;

	/**
	 * Determine if this class instance was modified in some way since it was constructed.
	 * 
	 * @var boolean
	 */
	protected $_dirty = false;

	protected $_fieldData = null;

	public function __construct(Model $Model, $field, $config = [])
	{
		$this->_field = $field;
		$this->_config = array_merge($config, $this->_defaults);

		$this->_model = &$Model->alias;

		// @todo this will go into a individual field preference - FieldData.FieldDataPreference
		if (isset($this->getModel()->filterArgs[$this->_field]['_config']['fieldData'])) {
			$this->_fieldData = $this->getModel()->filterArgs[$this->_field]['_config']['fieldData'];

			// if (!$this->_fieldData instanceof FieldDataEntity) {
			// 	$this->_fieldData = $Model->getFieldDataEntity($this->_fieldData);
			// }
		}
		else {
			// $this->_fieldData = $Model->getFieldDataEntity($field);
		}
	}

	public function getAdvancedFilterConfig()
	{
		// if advanced filter config doesnt exists (or is hidden object status)
		if (!isset($this->getModel()->filterArgs[$this->_field]['_config'])) {
			return false;
		}

		return $this->getModel()->filterArgs[$this->_field]['_config'];
	}

	// @todo this will go into a individual field preference - FieldData.FieldDataPreference
	public function getFieldDataConfig()
	{
		return $this->_fieldData;
	}
	public function getFieldDataObject()
	{
		$fieldData = $this->getFieldDataConfig();

		$processModel = $this->getModel();
		$processField = $this->getFieldName();

		if ($fieldData !== null) {
			$traverse = explode('.', $fieldData);
			$processField = array_pop($traverse);

			if (!empty($traverse)) {
				$_traverse = $traverse;

				foreach ($_traverse as $_t) {
					$processModel = $processModel->{$_t};
				}
				// $processModel = ClassRegistry::init($traverse[count($traverse)-1]);
			}
		}
		
		return $processModel->getFieldDataEntity($processField);
	}

	/**
	 * Get the model this field is attached to.
	 * 
	 * @return Model
	 */
	public function getModel() {
		return ClassRegistry::init($this->_model);
	}
	
	/**
	 * Get or set configuration for this field. @see CrudComponent::config()
	 *
	 * @return mixed Depending on the arguments.
	 */
	public function config($key = null, $value = null) {
		if ($key === null && $value === null) {
			return $this->_config;
		}

		if ($value === null) {
			if (is_array($key)) {
				$this->_config = Hash::merge($this->_config, $key);
				return $this;
			}

			return Hash::get($this->_config, $key);
		}

		if (is_array($value)) {
			$value = array_merge((array)Hash::get($this->_config, $key), $value);
		}

		return $this;
	}

	/**
	 * Builds a query parameter out of existing config of this field,
	 * which goes to '?' => [..] and can be used in Router::url() method. 
	 * 
	 * @return array Query parameter
	 */
	public function buildQueryParams() {
		$query = [
			$this->getFieldName() . '__comp_type' => $this->config('comparisonType'),
			$this->getFieldName() . '__show' => $this->config('show'),
			$this->getFieldName() => $this->getValue()
		];

		return $query;
	}

	public function getValue()
	{
		$value = $this->config('value');
		$config = $this->getAdvancedFilterConfig();

		if ($config !== false && $config['type'] == 'multiple_select') {
			// in case $value is already provided as array dont proceed explode it as per by default
			// occured in Compliance Management Dashboard Attribute class that specifically provides filter params
			if (is_string($value)) {
				if (strpos($value, ',') !== false) {
					$value = explode(',', $value);
				}
			}
		}

		if (is_bool($value)) {
			$value = (int) $value;
			$value = (string) $value;
		}

		return $value;
	}

	/**
	 * Get the field name as usable in filters.
	 * 
	 * @return string Field name.
	 */
	public function getFieldName() {
		return $this->_field;
	}

	/**
	 * Get field label.
	 * 
	 * @return string Label.
	 */
	public function getLabel() {
		$label = '';

		if (isset($this->getModel()->filterArgs[$this->_field]['_config']['name'])) {
			$label = $this->getModel()->filterArgs[$this->_field]['_config']['name'];
		}
		else {
			$label = $this->getFieldDataObject()->label();
		}

		return $label;
	}

	/**
	 * Get field type.
	 * 
	 * @return string Type.
	 */
	public function getType() {
		return $this->getModel()->filterArgs[$this->_field]['_config']['type'];
	}

	public function getOrder() {
		if (isset($this->getModel()->filterArgs[$this->_field]['_config']['order'])) {
			return $this->getModel()->filterArgs[$this->_field]['_config']['order'];
		}

		return self::DEFAULT_ORDER;
	}

	/**
	 * Put a comparison type value from current configuration into Model's filterArgs,
	 * to make it possible to do filtering.
	 * 
	 * @param string $value Comaprison type.
	 */
	public function setComparisonType($value) {
		$this->getModel()->filterArgs[$this->_field]['comp_type'] = $value;
		$this->_makeDirty();

		$conds = $value == FilterAdapter::COMPARISON_IS_NULL || $value == FilterAdapter::COMPARISON_IS_NOT_NULL;
		if ($conds) {
			$this->getModel()->filterArgs[$this->_field]['allowEmpty'] = true;
		}
	}

	/**
	 * Put a comparison type value from current configuration into Model's filterArgs,
	 * to make it possible to do filtering.
	 * 
	 * @param string $value Comaprison type.
	 */
	public function setShow($value) {
		$this->getModel()->filterArgs[$this->_field]['show'] = $value;
		$this->_makeDirty();
	}

	/**
	 * Gets the current comparison type as it is configured in this field.
	 * 
	 * @return string Comparison type.
	 */
	public function getComparisonType() {
		return $this->config('comparisonType');
	}

	/**
	 * Gets the current comparison type as it is configured in this field.
	 * 
	 * @return string Comparison type.
	 */
	public function getShow() {
		return $this->config('show');
	} 

	/**
	 * Clears the comparison type setting for this field on the model.
	 */
	public function unsetComparisonType() {
		unset($this->getModel()->filterArgs[$this->_field]['comp_type']);
		$this->_makeDirty();

		unset($this->getModel()->filterArgs[$this->_field]['allowEmpty']);
	}

	/**
	 * Clears the comparison type setting for this field on the model.
	 */
	public function unsetShow() {
		unset($this->getModel()->filterArgs[$this->_field]['show']);
		$this->_makeDirty();
	}

	/**
	 * Set each available preference before filtering.
	 */
	public function setPreferences() {
		foreach (self::$allowedPreferences as $preference) {
			$value = $this->config($preference);
			if ($value !== null) {
				// method would be e.g 'setComparisonType'
				$fn = self::preferenceMethod($preference);

				$this->{$fn}($value);
			}
		}
	}

	/**
	 * Rollbacks each available preference after filtering to not affect the ones processed after.
	 * 
	 * @return void
	 */
	public function resetPreferences() {
		if ($this->isDirty()) {
			foreach (self::$allowedPreferences as $preference) {
				$fn = self::preferenceMethod($preference, false);
				$this->{$fn}();
			}
		}
	}

	// builds the method name for a given preference
	public static function preferenceMethod($preference, $set = true) {
		$method = sprintf('set%s', ucfirst($preference));
		if ($set !== true) {
			$method = 'un' . $method;
		}

		return $method;
	}

	/**
	 * Check if this instance of a field and it's preferences has been touched or altered in some way already.
	 * 
	 * @return boolean True if a change has been made, False otherwise
	 */
	public function isDirty() {
		return $this->_dirty === true;
	}

	/**
	 * Method sets this field instance as modified and not in original congfiguration.
	 * 
	 * @return void
	 */
	protected function _makeDirty() {
		$this->_dirty = true;
	}

	/**
	 * Check if filter field has active filter.
	 * 
	 * @return boolean True if field has active filter.
	 */
	public function isActive() {
		$params = $this->buildQueryParams();

		$value = (isset($params[$this->getFieldName()])) ? $params[$this->getFieldName()] : null;
		$compType = (isset($params[$this->getFieldName() . '__comp_type'])) ? $params[$this->getFieldName() . '__comp_type'] : null;

		return ($value !== null && $value !== '') || in_array($compType, [FilterAdapter::COMPARISON_IS_NULL, FilterAdapter::COMPARISON_IS_NOT_NULL]);
	}


}
