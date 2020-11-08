<?php
App::uses('PieChart', 'Reports.Lib/Chart');
App::uses('Hash', 'Utility');

class MostUsedRelatedObjectsChart extends PieChart
{
	public function setData($subject)
	{
		$data = [];

		foreach ($subject->collection as $Item) {
			foreach ($Item->{$subject->field} as $Object) {
				$key = $Object->id;

				if (!isset($data[$key])) {
					$data[$key] = [
						'name' => $Object->{$Object->getModel()->displayField},
						'value' => 0
					];
				}

				$data[$key]['value']++;
			}
		}

		$data = Hash::sort($data, '{n}.value', 'desc');

		$data = array_slice($data, 0, 10);

		$this->addSeries(['data' => array_values($data)]);
	}
}