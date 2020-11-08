<?php
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');
App::uses('CakeText', 'Utility');

class SecurityPolicyItemData extends ItemDataEntity
{
	public function associationsChart()
	{
		$data = [
			'indicator' => [
				$this->SecurityService->getModel()->label(['singular' => true]),
				$this->Risk->getModel()->label(['singular' => true]),
				$this->ThirdPartyRisk->getModel()->label(['singular' => true]),
				$this->BusinessContinuity->getModel()->label(['singular' => true]),
				$this->ComplianceManagement->getModel()->label(['singular' => true]),
				$this->DataAsset->getModel()->label(['singular' => true]),
			],
			'data' => [
				$this->SecurityService->count(),
				$this->Risk->count(),
				$this->ThirdPartyRisk->count(),
				$this->BusinessContinuity->count(),
				$this->ComplianceManagement->count(),
				$this->DataAsset->count(),
			]
		];

		return $data;
	}

	public function reviewsTimelineChart()
	{
		$data = [
			'xAxis' => ReportBlockChartSetting::timelineMonths(),
			'yAxis' => SecurityPolicyReview::statuses(),
			'data' => ClassRegistry::init('SecurityPolicyReview')->timeline($this->getPrimary())
		];

		return $data;
	}

	public function relatedComplianceItemsChart()
	{
		$complianceData = [];

		foreach ($this->ComplianceManagement as $ComplianceManagement) {
			$Regulator = $ComplianceManagement->CompliancePackageItem->CompliancePackage->CompliancePackageRegulator;
			$CompliancePackageItem = $ComplianceManagement->CompliancePackageItem;

			if (!isset($complianceData[$Regulator->getPrimary()])) {
				$complianceData[$Regulator->getPrimary()] = [
					'name' => CakeText::truncate($Regulator->name, 50),
					'children' => []
				];
			}

			$complianceData[$Regulator->getPrimary()]['children'][] = [
				'name' => CakeText::truncate($CompliancePackageItem->item_id . ' ' . $CompliancePackageItem->name, 50),
			];
		}

		$data = [
			'data' => [
				[
					'name' => $this->index,
					'children' => array_values($complianceData)
				]
			]
		];

		return $data;
	}

	public function relatedRiskItemsChart()
	{
		$assocs = [
			'RiskIncident' => __('Incident Asset Risks'),
			'RiskTreatment' => __('Treatment Asset Risks'),
			'ThirdPartyRiskIncident' => __('Incident Third Party Risks'),
			'ThirdPartyRiskTreatment' => __('Treatments Third Party Risks'),
			'BusinessContinuityIncident' => __('Incidents Business Risks'),
			'BusinessContinuityTreatment' => __('Treatments Business Risks'),
		];

		$data = [
			'data' => [
				[
					'name' => $this->index,
					'children' => []
				]
			]
		];

		foreach ($assocs as $model => $label) {
			$items = [];

			$AssocCollection = $this->{$model};

			if (!empty($AssocCollection)) {
				foreach ($AssocCollection as $Item) {
					$items[] = [
						'name' => CakeText::truncate($Item->{$Item->getModel()->displayField}, 50)
					];
				}
			}

			$assocData = [
				'name' => (!empty($items)) ? $label : $label . ' ' . __('(Empty)'),
				'children' => $items
			];

			$data['data'][0]['children'][] = $assocData;
		}

		return $data;
	}
}