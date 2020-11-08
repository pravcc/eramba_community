<?php
App::uses('InjectedList', 'AdvancedFilters.Lib/Configuration');
App::uses('FilterGroupConfiguration', 'AdvancedFilters.Lib/Configuration');

class FilterConfiguration
{
	/**
	 * List of groups.
	 * 
	 * @var InjectedList
	 */
	protected $_groups = [];

	/**
	 * Construct, build groups list.
	 */
	public function __construct()
	{
		$this->_groups = new InjectedList();
	}

	/**
	 * Add group to group list.
	 * 
	 * @param FilterGroupConfiguration $group Group configuration object.
	 * @param array $options Options of insert as array.
	 *   - before: Name of the group before which this group will be placed.
	 *   - after: Name of the group after which this group will be placed.
	 * @return void
	 */
	public function addGroup(FilterGroupConfiguration $group, array $options = [])
	{
		if (isset($options['before'])) {
			$this->_groups->insertBefore($options['before'], $group->slug(), $group);
		}
		elseif (isset($options['after'])) {
			$this->_groups->insertAfter($options['after'], $group->slug(), $group);
		}
		else {
			$this->_groups->insert($group->slug(), $group);
		}
	}

	/**
	 * Get group from group list by slug.
	 * 
	 * @param  string $slug Group slug.
	 * @return FilterGroupConfiguration|null Group object, null if group with given slug doesnt exist.
	 */
	public function getGroup(string $slug)
	{
		return $this->_groups->get($slug);
	}

	/**
	 * Remove group from group list by slug.
	 * 
	 * @param  string $slug Group slug.
	 * @return void
	 */
	public function removeGroup(string $slug)
	{
		$this->_groups->remove($slug);
	}

	/**
	 * Returns configuration as array.
	 * 
	 * @return array AdvancedFilters configuration.
	 */
	public function toArray()
	{
		$config = [];

		foreach ($this->_groups as $group) {
			$groupConfig = [];

			foreach ($group->getFieldList() as $field) {
				$fieldConfig = [
					'type' => $field->type(),
					'name' => $field->label(),
					'show_default' => $field->showDefault(),
					'fieldData' => $field->fieldData(),
				];

				if ($field->findField() !== null && $field->returnField() !== null) {
					$fieldConfig['filter'] = [
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => $field->findField(),
						'field' => $field->returnField(),
					];
				}
				elseif ($field->statusField() !== null && $field->returnField() !== null) {
					$fieldConfig['filter'] = [
						'type' => 'subquery',
						'method' => 'findComplexType',
						'statusField' => $field->statusField(),
						'field' => $field->returnField(),
					];
				}
				else {
					$fieldConfig['filter'] = false;
				}

				if ($field->customField() !== null) {
					$fieldConfig['filter']['customField'] = $field->customField();
				}

				if ($field->options() !== null) {
					$fieldConfig['data']['callable'] = $field->options();
				}

				if ($field->userField() !== null) {
					$fieldConfig['filter']['userField'] = $field->userField();
				}

				$groupConfig[$field->name()] = $fieldConfig;
			}

			$config[$group->name()] = $groupConfig;
		}

		return $config;
	}
}