<?php
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');
App::uses('SecurityServiceItemData', 'Model/FieldData/Item');
App::uses('SecurityServiceAudit', 'Model');

class SecurityServiceItemCollection extends ItemDataCollection
{
	public function __construct(Model $Model)
	{
		parent::__construct($Model);
	}

	public function auditResultTimelineChart()
	{
		$data = [
			'xAxis' => ReportBlockChartSetting::timelineMonths(),
			'yAxis' => SecurityServiceAudit::results(),
			'data' => ClassRegistry::init('SecurityServiceAudit')->timeline($this->getAuditsIds())
		];

		return $data;
	}

	public function getAuditsIds()
	{
		$ids = [];
		
		foreach ($this as $Item) {
			$ids = array_merge($ids, $this->SecurityServiceAudit->getPrimaryKeys());
		}

		return $ids;
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
			'data' => [0, 0, 0, 0, 0, 0]
		];

		foreach ($this as $ItemData) {
			$itemChartData = $ItemData->associationsChart();
			$counter = 0;

			foreach ($itemChartData['data'] as $value) {
				$data['data'][$counter] = $data['data'][$counter] + $value;
				$counter++; 
			}
		}

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

		foreach ($this as $Item) {
			$itemChartData = $Item->auditResultsChart();
			addArrayValues($data['data'], $itemChartData['data']);
		}
		
		return $data;
	}
}