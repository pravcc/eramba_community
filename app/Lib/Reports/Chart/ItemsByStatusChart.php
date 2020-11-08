<?php
App::uses('PieChart', 'Reports.Lib/Chart');
App::uses('Hash', 'Utility');

class ItemsByStatusChart extends PieChart
{
	public function setData($subject)
	{
		$data = [];

		$Model = $subject->collection->getModel();

		foreach ($subject->collection as $Item) {
			foreach ($Model->getShowableObjectStatuses() as $status) {
				if (!empty($subject->statuses) && !in_array($status['field'], $subject->statuses)) {
					continue;
				}

				if (!isset($data[$status['field']])) {
					$data[$status['field']] = [
						'name' => $status['title'],
						'value' => 0
					];
				}

				if ($Item->getStatusValue($status['field'])) {
					$data[$status['field']]['value']++;
				}
			}
		}

		$this->addSeries(['data' => array_values($data)]);
	}
}