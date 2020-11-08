<?php
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');
App::uses('CakeText', 'Utility');

class AssetItemData extends ItemDataEntity
{
	public function relatedObjectsChart()
	{
		$assocs = [
			'Risk' => __('Asset Risks'),
			'ThirdPartyRisk' => __('Third Party Risks'),
			'SecurityIncident' => __('Incidents'),
			'ComplianceManagement' => __('Compliance Packages')
		];

		if (AppModule::loaded('AccountReviews')) {
			$assocs['AccountReview'] = __('Account Reviews');
		}

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

			if ($model == 'ComplianceManagement') {
				$packages = [];
				foreach (Hash::extract($this->getData(), 'ComplianceManagement.{n}.CompliancePackageItem.CompliancePackage.name') as $item) {
					if (!in_array($item, $packages)) {
						$items[] = [
							'name' => CakeText::truncate($item, 50)
						];
						$packages[] = $item;
					}
				}
			}
			elseif ($model == 'AccountReview') {
				$list = ClassRegistry::init('AccountReviews.AccountReview')->advancedFind('list', [
					'fields' => ['AccountReview.id', 'AccountReview.title'],
					'conditions' => [
						'AccountReview.id' => ClassRegistry::init('AccountReviewsAsset')->advancedFind('all', [
							'fields' => ['AccountReviewsAsset.account_review_id'],
							'conditions' => ['AccountReviewsAsset.asset_id' => $this->getPrimary()]
						])
					],
				])->get();

				foreach ($list as $item) {
					$items[] = [
						'name' => CakeText::truncate($item, 50)
					];
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