<?php
App::uses('PieChart', 'Reports.Lib/Chart');
App::uses('Hash', 'Utility');

class CollectionByProperty extends PieChart
{
	public function setData($subject)
	{
		$data = [];

		$displayField = '';

		foreach ($subject->collection as $Item) {
			if (empty($displayField)) {
				$displayField = $Item->getModel()->displayField;
			}

			$SubItem = $Item->{$subject->property};

			if (empty($SubItem)) {
				continue;
			}

			$value = $SubItem->{$displayField};

			if (!isset($data[$value])) {
				$data[$value] = [
					'name' => $value,
					'value' => 0
				];
			}

			$data[$value]['value']++;
		}

		$this->addSeries([
			'data' => array_values($data)
		]);
	}
}