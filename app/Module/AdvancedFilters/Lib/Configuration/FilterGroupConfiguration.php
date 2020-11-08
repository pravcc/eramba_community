<?php
App::uses('AbstractConfiguration', 'AdvancedFilters.Lib/Configuration');
App::uses('InjectedList', 'AdvancedFilters.Lib/Configuration');
App::uses('FilterFieldConfiguration', 'AdvancedFilters.Lib/Configuration');

class FilterGroupConfiguration extends AbstractConfiguration
{
	/**
	 * Configuration array. Configs are stored in associative array formated like [configName => configValue].
	 * 
	 * @var array
	 */
	protected $_configuration = [
		'slug' => null,
		'name' => null
	];

	/**
	 * List of fields.
	 * 
	 * @var InjectedList
	 */
	private $_fields = null;

	/**
	 * Construct, set slug, init list of fields.
	 * 
	 * @param string $slug Group slug.
	 */
	public function __construct(string $slug)
	{
		$this->_setConfig('slug', $slug);

		$this->_fields = new InjectedList();
	}

	/**
	 * Get slug.
	 * 
	 * @return string Slug.
	 */
	public function slug()
	{
		return $this->_getConfig('slug');
	}

	/**
	 * Get and set name.
	 * 
	 * @param  string|null $name Name value to set.
	 * @return string|null Name.
	 */
	public function name(string $name = null)
	{
		return $this->_config('name', $name);
	}

	/**
	 * Add field to field list.
	 * 
	 * @param FilterFieldConfiguration $field Field configuration object.
	 * @param array $options Options of insert as array.
	 *   - before: Name of the field before which this field will be placed.
	 *   - after: Name of the field after which this field will be placed.
	 * @return void
	 */
	public function addField(FilterFieldConfiguration $field, array $options = [])
	{
		if (isset($options['before'])) {
			$this->_fields->insertBefore($options['before'], $field->name(), $field);
		}
		elseif (isset($options['after'])) {
			$this->_fields->insertAfter($options['after'], $field->name(), $field);
		}
		else {
			$this->_fields->insert($field->name(), $field);
		}
	}

	/**
	 * Get field from field list by name.
	 * 
	 * @param  string $name Field name.
	 * @return FilterFieldConfiguration|null Field object, null if field with given name doesnt exist.
	 */
	public function getField(string $name)
	{
		return $this->_fields->get($name);
	}

	/**
	 * Get whole field list.
	 * 
	 * @return InjectedList Field list.
	 */
	public function getFieldList()
	{
		return $this->_fields;
	}

	/**
	 * Remove field from field list by name.
	 * 
	 * @param  string $name Field name.
	 * @return void
	 */
	public function removeField(string $name)
	{
		$this->_fields->remove($name);
	}
}