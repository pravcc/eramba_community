<?php
App::uses('BaseExceptionItemCollection', 'Model/FieldData/Collection');
App::uses('Hash', 'Utility');

class PolicyExceptionItemCollection extends BaseExceptionItemCollection
{
	public function topExceptionsChart()
	{
		$data = [];

		foreach ($this as $Item) {
			foreach ($Item->SecurityPolicy as $SecurityPolicy) {
				$key = $SecurityPolicy->id;

				if (!isset($data[$key])) {
					$data[$key] = [
						'label' => $SecurityPolicy->index,
						'count' => 0
					];
				}

				$data[$key]['count']++;
			}
		}

		$data = Hash::sort($data, '{n}.count', 'desc');

		$data = array_slice($data, 0, 10);

		return [
			'label' => Hash::extract($data, '{n}.label'),
			'data' => Hash::extract($data, '{n}.count')
		];
	}
}