<?php
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');
App::uses('DataAsset', 'Model');
App::uses('CakeText', 'Utility');

class DataAssetInstanceItemData extends ItemDataEntity
{

	/**
	 * Check if flows for a given Data Asset Instance is enabled.
	 * 
	 * @return boolean True or false.
	 */
	public function isFlowsEnabled()
	{
		$conds = $this->DataAssetSetting === null;
		$conds = $conds || method_exists($this->DataAssetSetting, 'getPrimary') && empty($this->DataAssetSetting->getPrimary());
		if ($conds) {
			return false;
		}

		return true;
	}

	public function dataFlowTreeChart()
	{
		$assocs = [
			'SecurityService' => __('Control'),
			'SecurityPolicy' => __('Policy'),
			'Risk' => __('Asset Risk'),
			'ThirdPartyRisk' => __('Third Party Risk'),
			'BusinessContinuity' => __('Business Continuity'),
			'Project' => __('Project'),
		];

		$data = [
			'data' => [
				[
					'name' => $this->Asset->name,
					'children' => []
				]
			]
		];

		foreach (DataAsset::statuses() as $statusId => $status) {
			$dataAssets = [];

			foreach ($this->DataAsset as $DataAsset) {
				if ($DataAsset->data_asset_status_id == $statusId) {
					$relatedObjects = [];

					foreach ($assocs as $assoc => $assocLabel) {
						foreach ($DataAsset->{$assoc} as $AssocItem) {
							$relatedObjects[] = [
								'name' => CakeText::truncate('[' . $assocLabel . '] ' . $AssocItem->{$AssocItem->getModel()->displayField}, 50),
								'children' => []
							];
						}
					}

					$dataAssets[] = [
						'name' => CakeText::truncate($DataAsset->title, 50),
						'children' => $relatedObjects
					];
				}
			}

			$data['data'][0]['children'][] = [
				'name' => $status,
				'children' => $dataAssets
			];
		}

		return $data;
	}
}