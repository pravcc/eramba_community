<?php
App::uses('AbstractConfiguration', 'AdvancedFilters.Lib/Configuration');

class FilterFieldConfiguration extends AbstractConfiguration
{
	/**
	 * Configuration array. Configs are stored in associative array formated like [configName => configValue].
	 * 
	 * @var array
	 */
	protected $_configuration = [
		'name' => null,
		'type' => null,
		'fieldData' => null,
		'label' => null,
		'findField' => null,
		'returnField' => null,
		'userField' => null,
		'statusField' => null,
		'customField' => null,
		'options' => null,
		'showDefault' => false,
	];

	/**
	 * Construct, set name.
	 * 
	 * @param string $slug Group slug.
	 */
	public function __construct(string $name)
	{
		$this->_setConfig('name', $name);
	}

	/**
	 * Get name.
	 * 
	 * @return string Name.
	 */
	public function name()
	{
		return $this->_getConfig('name');
	}

	/**
	 * Get and set type (text, number, date, select, multiple_select, object_status).
	 * 
	 * @param  string|null $type Type value to set.
	 * @return string|null Type.
	 */
	public function type(string $type = null)
	{
		return $this->_config('type', $type);
	}

	/**
	 * Get and set name of fieldData. In deeper relations use dot separated definition (Asset.AssetReview.planned_date).
	 * 
	 * @param  string|null $fieldData FieldData name to set.
	 * @return string|null FieldData.
	 */
	public function fieldData(string $fieldData = null)
	{
		return $this->_config('fieldData', $fieldData);
	}

	/**
	 * Get and set label.
	 * 
	 * @param  string|null $label Label to set.
	 * @return string|null Label.
	 */
	public function label(string $label = null)
	{
		return $this->_config('label', $label);
	}

	/**
	 * Get and set find field. In deeper relations use dot separated definition (Asset.AssetReview.planned_date).
	 * 
	 * @param  string|null $findField Find field to set.
	 * @return string|null Find field.
	 */
	public function findField(string $findField = null)
	{
		return $this->_config('findField', $findField);
	}

	/**
	 * Get and set return field.
	 * 
	 * @param  string|null $returnField Return field to set.
	 * @return string|null Return field.
	 */
	public function returnField(string $returnField = null)
	{
		return $this->_config('returnField', $returnField);
	}

	/**
	 * Get and set user field.
	 * 
	 * @param  string|null $userField User field to set.
	 * @return string|null User field.
	 */
	public function userField(string $userField = null)
	{
		return $this->_config('userField', $userField);
	}

	/**
	 * Get and set user object status field.
	 * 
	 * @param  string|null $statusField Object status field to set.
	 * @return string|null Object status field.
	 */
	public function statusField(string $statusField = null)
	{
		return $this->_config('statusField', $statusField);
	}

	/**
	 * Get and set custom field.
	 * 
	 * @param  string|null $customField Custom field to set.
	 * @return string|null Custom field.
	 */
	public function customField(string $customField = null)
	{
		return $this->_config('customField', $customField);
	}


	/**
	 * Get and set options for select, multiple_select type of field.
	 * 
	 * @param  callable|null $options Options callable.
	 * @return callable|null Options callable.
	 */
	public function options(callable $options = null)
	{
		return $this->_config('options', $options);
	}

	/**
	 * Get and set show default toggle.
	 * 
	 * @param  bool|null $options Show default.
	 * @return bool Show default.
	 */
	public function showDefault(bool $showDefault = null)
	{
		return $this->_config('showDefault', $showDefault);
	}
}