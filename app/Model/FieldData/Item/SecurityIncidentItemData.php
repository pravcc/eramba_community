<?php
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');
App::uses('CakeText', 'Utility');

class SecurityIncidentItemData extends ItemDataEntity
{
	public function relatedObjectsChart()
	{
		$assocs = [
			'Asset' => __('Assets'),
			'SecurityService' => __('Controls'),
			'AssetRisk' => __('Asset Risks'),
			'ThirdPartyRisk' => __('Third Party Risks'),
			'BusinessContinuity' => __('Business Continuities'),
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