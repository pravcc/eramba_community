<?php
App::uses('BaseExceptionItemCollection', 'Model/FieldData/Collection');
App::uses('Hash', 'Utility');

class RiskExceptionItemCollection extends BaseExceptionItemCollection
{
	public function topExceptionsChart()
	{
		$assoc = [
			'Risk' => __('Asset'),
			'ThirdPartyRisk' => __('Third Party'),
			'BusinessContinuity' => __('Business'),
		];

		$data = [];

		foreach ($this as $Item) {
			foreach ($assoc as $model => $label) {
				foreach ($Item->{$model} as $AssocItem) {
					$key = $model . '-' . $AssocItem->id;

					if (!isset($data[$key])) {
						$data[$key] = [
							'label' => sprintf('%s (%s)', $AssocItem->{ClassRegistry::init($model)->displayField}, $label),
							'count' => 0
						];
					}

					$data[$key]['count']++;
				}
			}
		}

		$data = Hash::sort($data, '{s}.count', 'desc');

		$data = array_slice($data, 0, 10);

		return [
			'label' => Hash::extract($data, '{s}.label'),
			'data' => Hash::extract($data, '{s}.count')
		];
	}
}