<?php
App::uses('DashboardAppModel', 'Dashboard.Model');
App::uses('Cron', 'Model');

class DashboardKpiValueLog extends DashboardAppModel {
	public $useTable = 'kpi_value_logs';

	public $belongsTo = [
		'DashboardKpiValue' => [
			'className' => 'Dashboard.DashboardKpiValue',
			'foreignKey' => 'kpi_value_id'
		],
		'DashboardKpi' => [
			'className' => 'Dashboard.DashboardKpi',
			'foreignKey' => 'kpi_id'
		]
	];

	public $weeklyQuery = 'DATE(DashboardKpiValueLog.created) >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
	public $dailyAverageField = 'ROUND(AVG(DashboardKpiValueLog.value),0)';
	public $dailyMaxValue = 'MAX(DashboardKpiValueLog.value)';

	/**
	 * Saves a log record for history of the KPI.
	 */
	public function saveLog($kpiValueId) {
		$this->DashboardKpiValue->id = $kpiValueId;
		$kpiId = $this->DashboardKpiValue->field('kpi_id');
		$value = $value = $this->DashboardKpi->calculate($kpiId);
		
		$saveData = [
			'kpi_value_id' => $kpiValueId,
			'kpi_id' => $kpiId,
			'value' => $value,
			'request_id' => Cron::requestId(), // CRON recalculation for chart data generation TODO
			'timestamp' => CakeTime::fromString('now')
		];
		
		$this->create($saveData);
		return $this->save();
	}
	
	public function getWeekMaxValue($kpiId) {
		$data = $this->find('all', [
			'conditions' => [
				'DashboardKpiValueLog.kpi_id' => $kpiId,
				$this->weeklyQuery
			],
			'fields' => [
				'MAX(DashboardKpiValueLog.value) as max_value'
			],
			'recursive' => -1
		]);

		return $data[0][0];
	}
	
	public function getSectionWeekMaxValue($model) {
		$data = $this->find('first', [
			'conditions' => [
				'DashboardKpi.model' => $model,
				$this->weeklyQuery
			],
			'fields' => [
				$this->dailyMaxValue . ' as daily_max_value'
				// 'MAX(DashboardKpiValueLog.value) as max_value'
			],
			'group' => [
				'DATE(DashboardKpiValueLog.created)',
			],
			'recursive' => 0
		]);

		return $data[0]['max_value'];
	}

}
