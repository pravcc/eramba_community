<?php
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');
App::uses('AwarenessProgramItemData', 'Model/FieldData/Item');
App::uses('Hash', 'Utility');

class AwarenessProgramItemCollection extends ItemDataCollection
{
	public function awarenessComplianceOverTimeChart()
	{
		$data = [
			'xAxis' => ReportBlockChartSetting::timeline('-1 year', 'now', '+1 week', 'Y-W', 'Y-m-d'),
			'label' => [],
			'data' => []
		];

		$programs = $this->getModel()->getList();

		foreach ($programs as $key => $item) {
			$activeUser = AwarenessProgramItemData::fieldOverTime('ActiveUser', $key);
			$ignoredUser = AwarenessProgramItemData::fieldOverTime('IgnoredUser', $key);
			$compliantUser = AwarenessProgramItemData::fieldOverTime('CompliantUser', $key);

			$awarenessData = [];

			foreach ($activeUser as $valKey => $value) {
				$usersCount = 0;
				$result = 0;

				if (isset($ignoredUser[$valKey]) && isset($compliantUser[$valKey])) {
					$usersCount = $activeUser[$valKey] - $ignoredUser[$valKey];

					if ($usersCount != 0) {
						$result = ($compliantUser[$valKey] / $usersCount) * 100;
					}
				}
				else {
					$result = null;
				}
				
				$awarenessData[$valKey] = $result;
			}

			$data['label'][] = $item;
			$data['data'][] = $awarenessData;
		}

		return $data;
	}
}