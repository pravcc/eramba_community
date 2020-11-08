<?php
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');
App::uses('SecurityServiceAudit', 'Model');
App::uses('CakeText', 'Utility');

class SecurityServiceItemData extends ItemDataEntity
{
	public function __construct(Model $Model, $data)
	{
		parent::__construct($Model, $data);
	}

	public function auditResultTimelineChart()
	{
		$data = [
			'xAxis' => ReportBlockChartSetting::timelineMonths(),
			'yAxis' => SecurityServiceAudit::results(),
			'data' => ClassRegistry::init('SecurityServiceAudit')->timeline($this->getPrimary())
		];

		return $data;
	}

	public function associationsChart()
	{
		$data = [
			'indicator' => [
				$this->Risk->getModel()->label(['singular' => true]),
				$this->ThirdPartyRisk->getModel()->label(['singular' => true]),
				$this->BusinessContinuity->getModel()->label(['singular' => true]),
				$this->ComplianceManagement->getModel()->label(['singular' => true]),
				$this->DataAsset->getModel()->label(['singular' => true]),
			],
			'data' => [
				$this->Risk->count(),
				$this->ThirdPartyRisk->count(),
				$this->BusinessContinuity->count(),
				$this->ComplianceManagement->count(),
				$this->DataAsset->count(),
			]
		];

		return $data;
	}

	public function auditResultsChart()
	{
		$data = [
			'label' => [
				__('Ok Audits'),
				__('Missing Audits'),
				__('Failed Audits')
			],
			'data' => [0, 0, 0]
		];

		foreach ($this->SecurityServiceAudit as $Audit) {
			if ($Audit->result == SecurityServiceAudit::RESULT_PASSED) {
				$data['data'][0]++;
			}
			elseif ($Audit->result === SecurityServiceAudit::RESULT_INCOMPLETE) {
				$data['data'][1]++;
			}
			else {
				$data['data'][2]++;
			}
		}

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
					'name' => $this->name,
					'children' => array_values($complianceData)
				]
			]
		];

		return $data;
	}

	public function relatedRiskItemsChart()
	{
		$assocs = [
			'Risk' => __('Asset Risks'),
			'ThirdPartyRisk' => __('Third Party Risks'),
			'BusinessContinuity' => __('Business Risks'),
		];

		$data = [
			'data' => [
				[
					'name' => $this->name,
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

	public function relatedPolicyItemsChart()
	{
		$assocs = [
			'SecurityPolicy' => __('Security Policies'),
		];

		$data = [
			'data' => [
				[
					'name' => $this->name,
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
						'name' => CakeText::truncate(sprintf("%s [%s]", $Item->{$Item->getModel()->displayField}, $Item->SecurityPolicyDocumentType->name), 50),
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