<?php
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');
App::uses('SecurityPolicyItemData', 'Model/FieldData/Item');

class SecurityPolicyItemCollection extends ItemDataCollection
{
	public function getControlConfig()
	{
		return [
			'callback' => [$this, 'associationsCount'],
			'key' => 'value'
		];
	}

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

	public function reviewsTimelineChart()
	{
		$data = [
			'xAxis' => ReportBlockChartSetting::timelineMonths(),
			'yAxis' => SecurityPolicyReview::statuses(),
			'data' => ClassRegistry::init('SecurityPolicyReview')->timeline($this->getReviewIds())
		];
		
		return $data;
	}

	public function getReviewIds()
	{
		$ids = [];
		
		foreach ($this as $Item) {
			$ids = array_merge($ids, $this->SecurityPolicyReview->getPrimaryKeys());
		}

		return $ids;
	}
}