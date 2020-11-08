<?php
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');

class AwarenessProgramItemData extends ItemDataEntity
{
	public function awarenessComplianceOverTimeChart()
	{
		$data = [
			'xAxis' => ReportBlockChartSetting::timeline('-1 year', 'now', '+1 week', 'Y-W', 'Y-m-d'),
			'label' => [__('Active Users'), __('Compliant Users')],
			'data' => [
				self::fieldOverTime('ActiveUser', $this->getPrimary()),
				self::fieldOverTime('CompliantUser', $this->getPrimary())
			]
		];

		return $data;
	}

	public static function fieldOverTime($field, $foreignKey)
	{
		$data = [];

		$timeline = array_map(function($val) {
			return null;
		}, ReportBlockChartSetting::timeline('-1 year', 'now', '+1 week', 'Y-W', 'Y-m-d'));

		$DashboardKpiValueLog = ClassRegistry::init('Dashboard.DashboardKpiValueLog');
		$DashboardKpi = ClassRegistry::init('Dashboard.DashboardKpi');
		$DashboardKpiAttribute = ClassRegistry::init('Dashboard.DashboardKpiAttribute');

		$query = $DashboardKpiValueLog->advancedFind('all', [
			'conditions' => [
				'DashboardKpiValueLog.kpi_id' => $DashboardKpi->advancedFind('all', [
					'conditions' => [
						'DashboardKpi.id' => $DashboardKpiAttribute->advancedFind('all', [
							'conditions' => [
								'OR' => [
									[
										'AND' => [
											'DashboardKpiAttribute.model' => 'AwarenessProgram',
											'DashboardKpiAttribute.foreign_key' => $foreignKey
										]
									],
									[
										'AND' => [
											'DashboardKpiAttribute.model' => 'AwarenessProgramUserModel',
											'DashboardKpiAttribute.foreign_key' => $field
										]
									]
								],
							],
							'group' => [
								'DashboardKpiAttribute.kpi_id HAVING count(DashboardKpiAttribute.kpi_id) = 2',
							],
							'fields' => [
								'DashboardKpiAttribute.kpi_id'
							]
						]),
						'DashboardKpi.model' => 'AwarenessProgram',
					],
					'fields' => [
						'DashboardKpi.id'
					]
				]),
				'DashboardKpiValueLog.created >=' => date(strtotime('-1 year')),
				'DashboardKpiValueLog.created <=' >= date(strtotime('now'))
			],
			'group' => [
				'DATE_FORMAT(DashboardKpiValueLog.created, "%Y-%u")'
			],
		]);

		$logValues = $query->get();

		foreach ($logValues as $item) {
			$timeline[date('Y-W', strtotime($item['DashboardKpiValueLog']['created']))] = $item['DashboardKpiValueLog']['value'];
		}

		return array_values($timeline);
	}
}