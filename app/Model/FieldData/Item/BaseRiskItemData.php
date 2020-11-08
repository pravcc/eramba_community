<?php
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');
App::uses('RiskClassification', 'Model');
App::uses('CakeText', 'Utility');

class BaseRiskItemData extends ItemDataEntity
{
	public function getRiskAppetiteThreshold()
	{
		if (empty($this->risk_appetite_threshold)) {
			$analysis = $this->getModel()->riskThreshold(
				$this->getPrimary(),
				RiskClassification::TYPE_ANALYSIS
			);
			
			$treatment = $this->getModel()->riskThreshold(
				$this->getPrimary(),
				RiskClassification::TYPE_TREATMENT
			);

			$this->risk_appetite_threshold = [$analysis, $treatment];
		}
		
		return $this->risk_appetite_threshold;
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
			],
			'hideValues' => true
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

		$itemData = $this->getData()[$classificationType];
		$itemData = Hash::sort($itemData, '{n}.RiskClassificationType.id');

		if (isset($itemData[0]) && isset($itemData[1])) {
			if (isset($data[$itemData[0]['id']][$itemData[1]['id']])) {
				$data[$itemData[0]['id']][$itemData[1]['id']]++;
			}
			else {
				$data[$itemData[0]['id']][$itemData[1]['id']] = 1;
			}
		}

		return $data;
	}

	public function relatedObjectsChart()
	{
		$assocs = [
			'Asset' => __('Assets'),
			'SecurityService' => __('Controls'),
			'SecurityPolicy' => __('Policies'),
			'Vulnerability' => __('Vulnerabilities'),
			'Threat' => __('Threats'),
			'RiskException' => __('Exceptions'),
		];

		$data = [
			'data' => [
				[
					'name' => $this->title,
					'children' => []
				]
			]
		];

		foreach ($assocs as $model => $label) {
			$AssocCollection = $this->{$model};

			$items = [];

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