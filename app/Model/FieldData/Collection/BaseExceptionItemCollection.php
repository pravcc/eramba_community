<?php
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');
App::uses('Hash', 'Utility');

class BaseExceptionItemCollection extends ItemDataCollection
{
	public function exceptionsByDurationChart()
	{
		$data = [
			'label' => [__('Count of Exceptions')],
			'xAxis' => [], 
			'data' => [[]]
		];

		$countData = [];

		foreach ($this as $key => $Item) {
			if (empty($Item->closure_date)) {
				continue;
			}

			$closeDate = new DateTime($Item->closure_date);
			$createdDate = new DateTime(date('Y-m-d', strtotime($Item->created)));

			$diff = $closeDate->diff($createdDate)->days;

			if (!isset($countData[$diff])) {
				$countData[$diff] = 1;
			}
			else {
				$countData[$diff]++;
			}
		}

		if (empty($countData)) {
			return $data;
		}

		$min = min(array_keys($countData));
		$max = max(array_keys($countData));
		$range = $max - $min;

		$stepsCount = 10;
		if ($range < $stepsCount) {
			$stepsCount = max($range, 1);
		}

		$step = ceil($range / $stepsCount);

		for ($i = 1; $i <= $stepsCount; $i++) {
			$left = $min + ($i-1) * $step;
			$right = $min + $i * $step - 1;
			if ($left > $right) {
				$right = $left;
			}
			$data['xAxis'][] = ($left != $right) ? $left . ' - ' . $right : $left;

			$count = 0;

			foreach ($countData as $days => $exceptionsCount) {
				if ($days >= $left && $days <= $right) {
					$count += $exceptionsCount;
				}
			}

			$data['data'][0][] = $count;
		}

		return $data;
	}
}