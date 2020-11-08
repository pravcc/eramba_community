<?php
App::uses('PieChart', 'Reports.Lib/Chart');
App::uses('Hash', 'Utility');
App::uses('SecurityServiceAudit', 'Model');
App::uses('ReportTemplate', 'Reports.Model');

class AuditResultsChart extends PieChart
{
	public function setData($subject)
	{
		$items = ($subject->config['templateType'] == ReportTemplate::TYPE_ITEM) ? [$subject->item] : $subject->collection;

		$percentage = (!empty($subject->percentage)) ? true : false;

		$labelSufix = ($percentage) ? ' %' : '';

		$data = [
			SecurityServiceAudit::RESULT_PASSED => [
				'name' => __('Ok Audits') . $labelSufix,
				'value' => 0,
			],
			SecurityServiceAudit::RESULT_INCOMPLETE => [
				'name' => __('Missing Audits') . $labelSufix,
				'value' => 0,
			],
			SecurityServiceAudit::RESULT_FAILED => [
				'name' => __('Failed Audits') . $labelSufix,
				'value' => 0,
			],
		];

		$auditsCount = 0;

		foreach ($items as $Item) {
			foreach ($Item->SecurityServiceAudit as $Audit) {
				if (empty($subject->year) || date('Y', strtotime($Audit->planned_date)) == $subject->year) {
					$auditsCount++;
					$data[$Audit->result]['value']++;
				}
			}
		}

		if (!empty($percentage)) {
			foreach ($data as $key => $item) {
				if ($data[$key]['value'] != 0) {
					$data[$key]['value'] = self::formatNumber(($data[$key]['value'] / $auditsCount) * 100);
				}
			}
		}

		$this->addSeries(['data' => array_values($data)]);
	}
}