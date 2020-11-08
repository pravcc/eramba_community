<?php
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');
App::uses('CakeText', 'Utility');

class ProjectItemData extends ItemDataEntity
{
	public function relatedObjectsChart()
	{
		$assocs = [
			'ProjectAchievement' => __('Tasks'),
			'SecurityService' => __('Controls'),
			'Risk' => __('Asset Risks'),
			'ThirdPartyRisk' => __('Third Party Risks'),
			'BusinessContinuity' => __('Business Continuities'),
			'SecurityPolicy' => __('Policies'),
			'DataAsset' => __('Data Flow Assets'),
			'ComplianceManagement' => __('Compliance Packages'),
			
		];

		$data = [
			'data' => [
				[
					'name' => CakeText::truncate($this->title, 50),
					'children' => []
				]
			]
		];

		foreach ($assocs as $model => $label) { 
			$items = [];

			if ($model == 'DataAsset') {
				$assets = [];
				foreach (Hash::extract($this->getData(), 'DataAsset.{n}.DataAssetInstance.Asset.name') as $item) {
					if (!in_array($item, $assets)) {
						$items[] = [
							'name' => CakeText::truncate($item, 50)
						];
						$assets[] = $item;
					}
				}
			}
			elseif ($model == 'ComplianceManagement') {
				$packages = [];
				foreach (Hash::extract($this->getData(), 'ComplianceManagement.{n}.CompliancePackageItem.CompliancePackage.CompliancePackageRegulator.name') as $item) {
					if (!in_array($item, $packages)) {
						$items[] = [
							'name' => CakeText::truncate($item, 50)
						];
						$packages[] = $item;
					}
				}
			}
			else {
				$AssocCollection = $this->{$model};

				if (!empty($AssocCollection)) {
					foreach ($AssocCollection as $Item) {
						$items[] = [
							'name' => CakeText::truncate($Item->{$Item->getModel()->displayField}, 50)
						];
					}
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