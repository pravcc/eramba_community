<?php
App::uses('FilterConfiguration', 'AdvancedFilters.Lib/Configuration');
App::uses('FilterFieldConfiguration', 'AdvancedFilters.Lib/Configuration');
App::uses('FilterGroupConfiguration', 'AdvancedFilters.Lib/Configuration');
App::uses('UserFieldsBehavior', 'UserFields.Model/Behavior');

/**
 * Helper class to easily build and modify advanced filter configuration.
 */
class FilterConfigurationBuilder
{
	/**
	 * Model instance.
	 * 
	 * @var Model
	 */
	private $_Model = null;

	/**
	 * FilterConfiguration instance.
	 * 
	 * @var FilterConfiguration
	 */
	private $_configuration = null;

	/**
	 * Instance of active FilterGroupConfiguration. 
	 * 
	 * @var FilterGroupConfiguration
	 */
	protected $_activeGroup = null;

	/**
	 * Construct, assign variables, create configuration instance.
	 * 
	 * @param Model $Model Model instance.
	 */
	public function __construct(Model $Model)
	{
		$this->_Model = $Model;
		$this->_configuration = new FilterConfiguration();
	}

	/**
	 * Create or edit field configuration.
	 * 
	 * @param  string $name Field name.
	 * @param  array $config Configuration array for AbstractConfiguration::config(). Special options:
	 *   - insertOptions: Insert options for FilterGroupConfiguration::addField().
	 * @param  bool $predefineData If we want to predefine data.
	 * @return FilterConfigurationBuilder This instance.
	 */
	public function field(string $name, array $config = [], bool $predefineData = false)
	{
		$field = $this->_activeGroup->getField($name);

		if ($field === null) {
			$field = new FilterFieldConfiguration($name);
		}

		if ($predefineData) {
			$config = array_merge($this->_getDefaultConfig($name), $config);
		}

		// handle insert options
		$insertOptions = [];
		if (isset($config['insertOptions'])) {
			$insertOptions = $config['insertOptions'];
			unset($config['insertOptions']);
		}

		$field->config($config);

		$this->_activeGroup->addField($field, $insertOptions);

		return $this;
	}

	/**
	 * Get default auto configuration based on model and given field name.
	 * 
	 * @param  string $fieldName Field name.
	 * @return array Default configuration.
	 */
	protected function _getDefaultConfig(string $fieldName)
	{
		$fieldName = str_replace('-', '.', $fieldName);

		$config = [
			'returnField' => "{$this->getModel()->alias}.id",
		];

		$config = array_merge($config, $this->_getFieldDataDefaultConfig($fieldName));

		return $config;
	}

	/**
	 * Get default configuration based on given FieldData field name.
	 * 
	 * @param  string $fieldDataName FieldData field name.
	 * @return array Default configuration.
	 */
	protected function _getFieldDataDefaultConfig(string $fieldDataName)
	{
		$config = [];

		$Model = $this->getModel();

		$fieldParts = explode('.', $fieldDataName);

		$middleAssocConfig = [];

		if (count($fieldParts) == 2 && !empty($Model->{$fieldParts[0]})) {
			$Model = $Model->{$fieldParts[0]};
			$fieldDataName = $fieldParts[1];
			$middleAssocConfig = $this->getModel()->getAssociated($fieldParts[0]);
		}

		if (count($fieldParts) > 2 || !$Model->hasFieldDataEntity($fieldDataName)) {
			return $config;
		}

		$fieldData = $Model->getFieldDataEntity($fieldDataName);

		$assocConfig = [];

		if ($fieldData->isAssociated()) {
			$assocConfig = $fieldData->getAssociationConfig();
		}

		$config['findField'] = "{$Model->alias}.{$fieldData->getFieldName()}";
		$config['fieldData'] = $fieldData->getFieldName();
		$config['label'] = $fieldData->getLabel();

		if ($Model->alias != $this->getModel()->alias) {
			$config['fieldData'] = "{$Model->alias}.{$fieldData->getFieldName()}";
		}

		if (!empty($assocConfig) && in_array($assocConfig['association'], ['hasOne', 'hasMany', 'hasAndBelongsToMany'])) {
			$AssocModel = $Model->{$fieldData->getAssociationModel()};
			$config['findField'] = "{$AssocModel->alias}.id";

			if (!empty($middleAssocConfig)) {
				$config['findField'] = "{$fieldParts[0]}.{$config['findField']}";
			}
		}

		if (!empty($middleAssocConfig) && $middleAssocConfig['association'] == 'belongsTo') {
			$config['returnField'] = "{$this->getModel()->alias}.{$middleAssocConfig['foreignKey']}";
		}

		return $config;
	}

	/**
	 * Create or edit group configuration. Touched group is set as active working group to _activeGroup property.
	 * 
	 * @param  string $name Field name.
	 * @param  array $config Configuration array for AbstractConfiguration::config(). Special options:
	 *   - insertOptions: Insert options for FilterGroupConfiguration::addField().
	 * @return FilterConfigurationBuilder This instance.
	 */
	public function group(string $slug, array $config = [])
	{
		$group = $this->getConfiguration()->getGroup($slug);

		if ($group === null) {
			$group = new FilterGroupConfiguration($slug);
		}

		// handle insert options
		$insertOptions = [];
		if (isset($config['insertOptions'])) {
			$insertOptions = $config['insertOptions'];
			unset($config['insertOptions']);
		}

		$group->config($config);

		$this->getConfiguration()->addGroup($group, $insertOptions);

		$this->_activeGroup = $group;

		return $this;
	}

	/**
	 * Create or edit field, force text configuration.
	 * 
	 * @param  string $name Field name.
	 * @param  array $config Configuration array for AbstractConfiguration::config(). Special options:
	 *   - insertOptions: Insert options for FilterGroupConfiguration::addField().
	 * @param  bool $predefineData If we want to predefine data.
	 * @return FilterConfigurationBuilder This instance.
	 */
	public function textField(string $name, array $config = [], bool $predefineData = true)
	{
		$config = array_merge([
			'type' => 'text'
		], $config);

		return $this->field($name, $config, $predefineData);
	}

	/**
	 * Create or edit field, force number configuration.
	 * 
	 * @param  string $name Field name.
	 * @param  array $config Configuration array for AbstractConfiguration::config(). Special options:
	 *   - insertOptions: Insert options for FilterGroupConfiguration::addField().
	 * @param  bool $predefineData If we want to predefine data.
	 * @return FilterConfigurationBuilder This instance.
	 */
	public function numberField(string $name, array $config = [], bool $predefineData = true)
	{
		$config = array_merge([
			'type' => 'number'
		], $config);

		return $this->field($name, $config, $predefineData);
	}

	/**
	 * Create or edit field, force date configuration.
	 * 
	 * @param  string $name Field name.
	 * @param  array $config Configuration array for AbstractConfiguration::config(). Special options:
	 *   - insertOptions: Insert options for FilterGroupConfiguration::addField().
	 * @param  bool $predefineData If we want to predefine data.
	 * @return FilterConfigurationBuilder This instance.
	 */
	public function dateField(string $name, array $config = [], bool $predefineData = true)
	{
		$config = array_merge([
			'type' => 'date'
		], $config);

		return $this->field($name, $config, $predefineData);
	}

	/**
	 * Create or edit field, force select configuration.
	 * 
	 * @param  string $name Field name.
	 * @param  callable $options Callable returning options list for filter field.
	 * @param  array $config Configuration array for AbstractConfiguration::config(). Special options:
	 *   - insertOptions: Insert options for FilterGroupConfiguration::addField().
	 * @param  bool $predefineData If we want to predefine data.
	 * @return FilterConfigurationBuilder This instance.
	 */
	public function selectField(string $name, callable $options, array $config = [], bool $predefineData = true)
	{
		$config = array_merge([
			'type' => 'select',
			'options' => $options,
		], $config);

		return $this->field($name, $config, $predefineData);
	}

	/**
	 * Create or edit field, force multiple_select configuration.
	 * 
	 * @param  string $name Field name.
	 * @param  callable $options Callable returning options list for filter field.
	 * @param  array $config Configuration array for AbstractConfiguration::config(). Special options:
	 *   - insertOptions: Insert options for FilterGroupConfiguration::addField().
	 * @param  bool $predefineData If we want to predefine data.
	 * @return FilterConfigurationBuilder This instance.
	 */
	public function multipleSelectField(string $name, callable $options, array $config = [], bool $predefineData = true)
	{
		$config = array_merge([
			'type' => 'multiple_select',
			'options' => $options,
		], $config);

		return $this->field($name, $config, $predefineData);
	}

	/**
	 * Create or edit field, force non filterable field configuration (Fields with live calculation logic cannot be filterable).
	 * 
	 * @param  string $name Field name.
	 * @param  array $config Configuration array for AbstractConfiguration::config(). Special options:
	 *   - insertOptions: Insert options for FilterGroupConfiguration::addField().
	 * @param  bool $predefineData If we want to predefine data.
	 * @return FilterConfigurationBuilder This instance.
	 */
	public function nonFilterableField(string $name, array $config = [], bool $predefineData = true)
	{
		$config = array_merge([
			'type' => 'text',
			'findField' => null,
			'returnField' => null,
		], $config);

		return $this->field($name, $config, $predefineData);
	}

	/**
	 * Create or edit field, force user field configuration.
	 * 
	 * @param  string $name Field name.
	 * @param  string $userField User field name.
	 * @param  array $config Configuration array for AbstractConfiguration::config(). Special options:
	 *   - insertOptions: Insert options for FilterGroupConfiguration::addField().
	 * @param  bool $predefineData If we want to predefine data.
	 * @return FilterConfigurationBuilder This instance.
	 */
	public function userField(string $name, string $userField, array $config = [], bool $predefineData = true)
	{
		$config = array_merge([
			'type' => 'multiple_select',
			'findField' => 'UserFieldsObject' . $userField . '.object_key',
			'options' => ['UserFieldsBehavior', 'getUsersGroupsOptionsList'],
			'userField' => $userField,
		], $config);

		return $this->field($name, $config, $predefineData);
	}

	/**
	 * Create or edit field, force object status configuration.
	 *
	 * @param  string $name Field name.
	 * @param  string $objectStatus Object status field.
	 * @param  array $config Configuration array for AbstractConfiguration::config(). Special options:
	 *   - insertOptions: Insert options for FilterGroupConfiguration::addField().
	 * @param  bool $predefineData If we want to predefine data.
	 * @return FilterConfigurationBuilder This instance.
	 */
	public function objectStatusField(string $name, string $objectStatus, array $config = [], bool $predefineData = true)
	{
		$statusConfig = $this->getModel()->Behaviors->ObjectStatus->field($this->getModel(), $objectStatus);

		if (isset($statusConfig['hidden']) && $statusConfig['hidden'] === true) {
			return $this;
		}

		$config = array_merge([
			'type' => 'object_status',
			'options' => [$this->getModel(), 'getStatusFilterOption'],
			'label' => $statusConfig['title'],
			'statusField' => $objectStatus,
			'findField' => null, // overwrite findField with empty value
		], $config);

		return $this->field($name, $config, $predefineData);
	}

	/**
	 * Get configuration.
	 * 
	 * @return FilterConfiguration Configuration object.
	 */
	public function getConfiguration()
	{
		return $this->_configuration;
	}

	/**
	 * Get model instance.
	 * 
	 * @return Model Working model instance.
	 */
	public function getModel()
	{
		return $this->_Model;
	}
}