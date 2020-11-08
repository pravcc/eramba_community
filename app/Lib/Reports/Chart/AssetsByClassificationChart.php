<?php
App::uses('SunburstChart', 'Reports.Lib/Chart');
App::uses('Hash', 'Utility');

class AssetsByClassificationChart extends SunburstChart
{
	public function setData($subject)
	{
		$data = [];

		foreach ($subject->collection as $Asset) {
			foreach ($Asset->AssetClassification as $Classification) {
				if (!isset($data[$Classification->asset_classification_type_id])) {
					$data[$Classification->AssetClassificationType->id] = [
						'name' => $Classification->AssetClassificationType->name,
						'value' => 0,
						'children' => []
					];
				}

				if (!isset($data[$Classification->asset_classification_type_id]['children'][$Classification->id])) {
					$data[$Classification->asset_classification_type_id]['children'][$Classification->id] = [
						'name' => $Classification->name,
						'value' => 0,
						'children' => [
							[
								'name' => 0,
								'value' => 0,
							]
						]
					];
				}

				$data[$Classification->AssetClassificationType->id]['value']++;
				$data[$Classification->asset_classification_type_id]['children'][$Classification->id]['value']++;
				$data[$Classification->asset_classification_type_id]['children'][$Classification->id]['children'][0]['value']++;
				$data[$Classification->asset_classification_type_id]['children'][$Classification->id]['children'][0]['name']++;
			}
		}

		foreach ($data as $key => $item) {
			$data[$key]['children'] = array_values($data[$key]['children']);
		}

		$this->addSeries([
			'data' => array_values($data)
		]);
	}
}