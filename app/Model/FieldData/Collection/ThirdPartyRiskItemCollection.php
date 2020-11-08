<?php
App::uses('BaseRiskItemCollection', 'Model/FieldData/Collection');

class ThirdPartyRiskItemCollection extends BaseRiskItemCollection
{
	public function thirdPartiesChart()
	{
		$data = [
			'label' => [],
			'data' => []
		];

		foreach ($this as $Item) {
			foreach ($Item->ThirdParty as $ThirdPartyItem) {
				if (isset($data['data'][$ThirdPartyItem->getPrimary()])) {
					$data['data'][$ThirdPartyItem->getPrimary()]++;
				}
				else {
					$data['label'][$ThirdPartyItem->getPrimary()] = $ThirdPartyItem->name;
					$data['data'][$ThirdPartyItem->getPrimary()] = 1;
				}
			}
		}

		return $data;
	}
}