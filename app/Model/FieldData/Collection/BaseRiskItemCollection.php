<?php
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');
App::uses('BaseRisk', 'Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');
App::uses('RiskClassification', 'Model');
App::uses('BaseRisk', 'Model');
App::uses('Hash', 'Utility');

class BaseRiskItemCollection extends ItemDataCollection
{
	public function riskScoreChart()
	{
		$data = [
			'xAxis' => ReportBlockChartSetting::timeline('-1 year', 'now', '+1 week', 'Y-W', 'Y-m-d'),
			'label' => [
				__('Risk Score'),
                __('Residual Risk Score'),
			],
			'data' => [
				$this->_fieldOverTime('total_risk_score'),
				$this->_fieldOverTime('total_residual_score')
			]
		];

		return $data;
	}

	protected function _fieldOverTime($field)
	{
		$data = [];

		$timeline = array_map(function($val) {return null;}, ReportBlockChartSetting::timeline('-1 year', 'now', '+1 week', 'Y-W', 'Y-m-d'));

		$DashboardKpiValueLog = ClassRegistry::init('Dashboard.DashboardKpiValueLog');
		$DashboardKpi = ClassRegistry::init('Dashboard.DashboardKpi');
		$DashboardKpiAttribute = ClassRegistry::init('Dashboard.DashboardKpiAttribute');

		$query = $DashboardKpiValueLog->advancedFind('all', [
			'conditions' => [
				'DashboardKpiValueLog.kpi_value_id' => $DashboardKpi->advancedFind('all', [
					'conditions' => [
						'DashboardKpi.id' => $DashboardKpiAttribute->advancedFind('all', [
							'conditions' => [
								'DashboardKpiAttribute.foreign_key' => $field 
							],
							'fields' => [
								'DashboardKpiAttribute.kpi_id'
							]
						]),
						'DashboardKpi.model' => $this->getModel()->alias
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

	public function classificationsMatrixChart()
	{
		$classifications = $this->getModel()->getAttachedClassifications();
		
		if (count($classifications) !== 2) {
			throw new ChartException(__('Cannot display chart with recent risk calculation configuration.'));
		}

		$axis = array_values($classifications);
		$axisName = array_values($this->getModel()->getAttachedClassifications(true));

		$data = [
			'xAxis' => [
				'name' => $axisName[0],
				'data' => $axis[0]
			],
			'yAxis' => [
				'name' => $axisName[1],
				'data' => $axis[1]
			],
			'label' => [
				__('Analysis'),
                __('Treatment'),
			],
			'data' => [
				$this->_riskClassificationMatrix($classifications, 'RiskClassification'),
				$this->_riskClassificationMatrix($classifications, 'RiskClassificationTreatment'),
			]
		];

		return $data;
	}

	public function classificationsTresholdsMatrixChart()
	{
		$data = $this->classificationsMatrixChart();

		$data['tresholds'] = $this->getModel()->getAttachedTresholds();

		return $data;
	}

	protected function _riskClassificationMatrix($classifications, $classificationType)
	{
		$types = array_keys($classifications);

		$data = [];

		$with = $this->getModel()->getAssociated('RiskClassification')['with'];

		foreach ($this as $Item) {
			$itemData = $Item->getData()[$classificationType];
			$itemData = Hash::sort($itemData, '{n}.RiskClassificationType.id');

			if (isset($itemData[0]) && isset($itemData[1])) {
				if (isset($data[$itemData[0]['id']][$itemData[1]['id']])) {
					$data[$itemData[0]['id']][$itemData[1]['id']]++;
				}
				else {
					$data[$itemData[0]['id']][$itemData[1]['id']] = 1;
				}
			}
		}

		return $data;
	}

	public function mitigationStrategiesChart()
	{
		$data = [
			'indicator' => [],
			'data' => []
		];

		$strategies = BaseRisk::mitigationStrategies();

		$data['indicator'] = $strategies;
		$data['data'] = array_map(function($val) {return 0;}, $strategies);

		foreach ($this as $ItemData) {
			$data['data'][$ItemData->risk_mitigation_strategy_id]++;
		}

		return $data;
	}

	public function tagsChart()
	{
		$data = [
			'label' => [],
			'data' => []
		];

		foreach ($this as $Item) {
			foreach ($Item->Tag as $Tag) {
				if (isset($data['data'][$Tag->title])) {
					$data['data'][$Tag->title]++;
				}
				else {
					$data['label'][$Tag->title] = $Tag->title;
					$data['data'][$Tag->title] = 1;
				}
			}
		}

		return $data;
	}

	public function businessUnitsChart()
	{
		$data = [
			'label' => [],
			'data' => []
		];

		foreach ($this as $Item) {
			$itemData = [];

			foreach ($Item->Asset as $AssetItem) {
				if (!empty($AssetItem->BusinessUnit)) {
					foreach ($AssetItem->BusinessUnit as $BusinessUnitItem) {
						$itemData[$BusinessUnitItem->getPrimary()] = $BusinessUnitItem->getPrimary();

						// set label
						$data['label'][$BusinessUnitItem->getPrimary()] = $BusinessUnitItem->name;
					}
				}
			}

			foreach ($itemData as $buId) {
				if (!isset($data['data'][$buId])) {
					$data['data'][$buId] = 1;
				}
				else {
					$data['data'][$buId]++;
				}
			}

		}

		return $data;
	}

	public function topRiskStakeholdersChart()
	{
		return $this->topRiskOwnersChart('Stakeholder');
	}

	public function topRiskOwnersChart($role = 'Owner')
	{
		$owners = [];

		foreach ($this as $Item) {
			foreach ($Item->{$role} as $Owner) {
				$key = 'User-' . $Owner->id;

				if (!isset($owners[$key])) {
					$owners[$key] = [
						'label' => __('%s (User)', $Owner->full_name),
						'count' => 0
					];
				}

				$owners[$key]['count']++;
			}

			foreach ($Item->{$role . 'Group'} as $Owner) {
				$key = 'Group-' . $Owner->id;

				if (!isset($owners[$key])) {
					$owners[$key] = [
						'label' => __('%s (Group)', $Owner->name),
						'count' => 0
					];
				}

				$owners[$key]['count']++;
			}
		}

		$owners = Hash::sort($owners, '{s}.count', 'desc');

		$owners = array_slice($owners, 0, 20);

		$data = [
			'label' => Hash::extract($owners, '{s}.label'),
			'data' => Hash::extract($owners, '{s}.count')
		];

		return $data;
	}

	public function riskScoreByStakeholderChart()
	{
		return $this->riskScoreByOwnerChart('Stakeholder');
	}

	public function riskScoreByOwnerChart($role = 'Owner')
	{
		$owners = [];

		$scoreSum = 0;

		foreach ($this as $Item) {
			foreach ($Item->{$role} as $Owner) {
				$key = 'User-' . $Owner->id;

				if (!isset($owners[$key])) {
					$owners[$key] = [
						'label' => __('%s (User)', $Owner->full_name),
						'count' => 0
					];
				}

				$owners[$key]['count'] += $Item->risk_score;
			}

			foreach ($Item->{$role . 'Group'} as $Owner) {
				$key = 'Group-' . $Owner->id;

				if (!isset($owners[$key])) {
					$owners[$key] = [
						'label' => __('%s (Group)', $Owner->name),
						'count' => 0
					];
				}

				$owners[$key]['count'] += $Item->risk_score;
			}

			$scoreSum += $Item->risk_score;
		}

		$averageScore = 0;

		// set average value for order
		if ($this->count() > 0) {
			$averageScore = ($scoreSum / $this->count());
			if (floor($averageScore) != $averageScore) {
				$averageScore = number_format($averageScore, 2, '.', '');
			}

			$owners['average'] = [
				'label' => __('Average Item Risk Score'),
				'count' => $averageScore
			];
		}

		$owners = Hash::sort($owners, '{s}.count', 'asc');

		// set style average value
		if ($this->count() > 0) {
			$owners['average']['count'] = ['value' => ($averageScore), 'itemStyle' => ['color' => '#005d7f']];
		}

		$data = [
			'label' => [__('Risk Score')],
			'yAxis' => Hash::extract($owners, '{s}.label'),
			'data' => [
				Hash::extract($owners, '{s}.count'),
			],
		];

		return $data;
	}
}