<?php
App::uses('DashboardAppModel', 'Dashboard.Model');
App::uses('DashboardKpi', 'Dashboard.Model');

class DashboardKpiValue extends DashboardAppModel {
	public $useTable = 'kpi_values';

	public $belongsTo = [
		'DashboardKpi' => [
			'className' => 'Dashboard.DashboardKpi',
			'foreignKey' => 'kpi_id'
		]
	];

	public $hasMany = [
		'DashboardKpiValueLog' => [
			'className' => 'Dashboard.DashboardKpiValueLog',
			'foreignKey' => 'kpi_value_id'
		]
	];

	public function afterSave($created, $options = array()) {
		// log value overtime
		///return $this->DashboardKpiValueLog->saveLog($this->id, $this->field('value'));
	}

	/**
	 * Recalculates and saves value of user's KPI.
	 *
	 * @param boolean $storeLog True to store a overtime log record into the dashboard kpi value logs table.
	 * @deprecated use DashboardKpi::recalculate();
	 */
	public function recalculate($kpiId) {
		$conds = [
			'kpi_id' => $kpiId
		];

		$exist = $this->find('first', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		$saveData = $conds;
		$saveData['value'] = $this->DashboardKpi->calculate($kpiId);

		if ($exist) {
			$saveData['id'] = $exist['DashboardKpiValue']['id'];
		}
		
		$this->create($saveData);
		return $this->save();
	}

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
}
