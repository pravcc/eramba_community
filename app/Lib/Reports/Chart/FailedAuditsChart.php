<?php
App::uses('BarChart', 'Reports.Lib/Chart');
App::uses('Hash', 'Utility');
App::uses('SecurityServiceAudit', 'Model');

class FailedAuditsChart extends BarChart
{
	public function setData($subject)
	{
		$data = [];

		foreach ($subject->collection as $Item) {
			$data[$Item->id] = [
				'label' => $Item->name,
				'count' => 0,
				'failedCount' => 0,
				'failedPercentage' => 0
			];

			foreach ($Item->SecurityServiceAudit as $Audit) {
				$yearCheck = (empty($subject->year) || (date('Y', strtotime($Audit->planned_date)) == $subject->year)) ? true : false;

				if ($yearCheck) {
					$data[$Item->id]['count']++;
				}

				if ($Audit->result == SecurityServiceAudit::RESULT_FAILED && $Audit->result !== null && $yearCheck) {
					$data[$Item->id]['failedCount']++;
				}

				$data[$Item->id]['failedPercentage'] = self::formatNumber(($data[$Item->id]['failedCount'] / $data[$Item->id]['count']) * 100);
			}
		}

		$valueField = (!empty($subject->percentage)) ? 'failedPercentage' : 'failedCount';

		$data = array_slice(Hash::sort($data, '{n}.' . $valueField, 'desc'), 0, 10);
		$data = Hash::sort($data, '{n}.' . $valueField, 'asc');

		$this->yAxisData(self::breakWords(Hash::extract($data, '{n}.label'), 25, 60));

		$this->addSeries([
			'name' => (!empty($subject->percentage)) ? __('Percentage of Failed Audits') : __('Count of Failed Audits'),
			'data' => Hash::extract($data, '{n}.' . $valueField),
			'stack' => 1
		]);

		if (empty($subject->percentage)) {
			$this->addSeries([
				'name' => __('Count of all Audits'),
				'data' => Hash::extract($data, '{n}.count'),
				'stack' => 2
			]);
		}
	}
}