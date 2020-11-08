<?php
App::uses('BaseExceptionItemCollection', 'Model/FieldData/Collection');
App::uses('Hash', 'Utility');

class ComplianceExceptionItemCollection extends BaseExceptionItemCollection
{
	public function topExceptionsChart()
	{
		$data = [];

		foreach ($this as $Item) {
			$exceptionData = [];

			foreach ($Item->ComplianceManagement as $ComplianceManagement) {
				if (!empty($ComplianceManagement->CompliancePackageItem)
					&& !empty($ComplianceManagement->CompliancePackageItem->CompliancePackage)
					&& !empty($ComplianceManagement->CompliancePackageItem->CompliancePackage->CompliancePackageRegulator)
				) {
					$key = $ComplianceManagement->CompliancePackageItem->CompliancePackage->CompliancePackageRegulator->id;

					if (!isset($exceptionData[$key])) {
						$exceptionData[$key] = [
							'label' => $ComplianceManagement->CompliancePackageItem->CompliancePackage->CompliancePackageRegulator->name,
							'count' => 1
						];
					}
				}
			}

			foreach ($exceptionData as $key => $value) {
				if (!isset($data[$key])) {
					$data[$key] = $value;
				}
				else {
					$data[$key]['count'] += $value['count'];
				}
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
