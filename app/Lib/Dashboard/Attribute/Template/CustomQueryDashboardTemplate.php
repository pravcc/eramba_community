<?php
App::uses('ClassRegistry', 'Utility');
App::uses('DashboardTemplate', 'Dashboard.Lib/Dashboard/Attribute/Template');

class CustomQueryDashboardTemplate extends DashboardTemplate {

	/**
	 * Helper method normalizes template configuration array and returns final config.
	 * 
	 * @param  array $parameters  Array with the attribute template config.
	 * @return array              Normalized configuration.
	 */
	protected function _normalize($parameters) {
		$parameters = parent::_normalize($parameters);

		return Hash::merge([
			'query' => []
		], $parameters);
	}

}