<?php
App::uses('BarChart', 'Reports.Lib/Chart');
App::uses('Hash', 'Utility');
App::uses('ReportTemplate', 'Reports.Model');

class RiskCostsChart extends BarChart
{
	public function setData($subject)
	{
		$costs = [];

		foreach ($subject->collection as $Item) {
			foreach ($Item->SecurityService as $SecurityService) {
				if (!isset($costs[$SecurityService->id])) {
					$costs[$SecurityService->id] = [
						'costs' => $SecurityService->{$subject->field},
						'count' => 1
					];
				}
				else {
					$costs[$SecurityService->id]['count']++;
				}
			}
		}

		$data = [];

		foreach ($subject->collection as $Item) {
			$itemCosts = 0;

			foreach ($Item->SecurityService as $SecurityService) {
				$itemCosts += $costs[$SecurityService->id]['costs'] / $costs[$SecurityService->id]['count'];
			}

			$itemCosts = [
				'value' => self::formatNumber($itemCosts)
			];

			if ($subject->config['templateType'] == ReportTemplate::TYPE_ITEM && !empty($subject->item) && $subject->item->id == $Item->id) {
				$itemCosts['itemStyle'] = [
					'color' => '#1c59bc'
				];
			}

			$data[] = [
				'label' => self::breakWords($Item->title, 25, 60),
				'data' => $itemCosts
			];
		}

		// sort results
		$data = Hash::sort($data, '{n}.data.value', 'asc');

		$this->addSeries([
			'name' => __('Costs %s', strtoupper($subject->field)),
			'data' => Hash::extract($data, '{n}.data')
		]);
		$this->yAxisData(Hash::extract($data, '{n}.label'));
	}
}