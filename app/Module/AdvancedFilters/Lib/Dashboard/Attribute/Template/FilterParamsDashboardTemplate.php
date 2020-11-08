<?php
App::uses('ClassRegistry', 'Utility');
App::uses('DashboardTemplate', 'Dashboard.Lib/Dashboard/Attribute/Template');

class FilterParamsDashboardTemplate extends DashboardTemplate {

	/**
	 * Gets the correct Model to build the queries on.
	 * 
	 * @param  Model  $Model Primary model
	 * @return Model         Model to make the queries on
	 */
	public function findOn(Model $Model) {
		$findOn = $this->get('findOn');
		if ($findOn !== null) {
			$Model = ClassRegistry::init($findOn);
		}

		if (!$Model->Behaviors->loaded('AdvancedFilters.AdvancedFilters')) {
			$Model->Behaviors->load('AdvancedFilters.AdvancedFilters');
		}

		return $Model;
	}

	/**
	 * Gets the column name to retrieve the query results from.
	 * 
	 * @param  Model  $Model Model
	 * @return string        Column name for the query filter field
	 */
	public function findField(Model $Model) {
		$findField = $this->get('findField');
		if ($findField === null) {
			return $this->findOn($Model)->primaryKey;
		}

		return $findField;
	}

	/**
	 * Helper method normalizes template configuration array and returns final config.
	 * 
	 * @param  array $parameters  Array with the attribute template config.
	 * @return array              Normalized configuration.
	 */
	protected function _normalize($parameters) {
		$parameters = parent::_normalize($parameters);

		return Hash::merge([
			'findOn' => null,
			'findField' => null,
			'params' => []
		], $parameters);
	}

}