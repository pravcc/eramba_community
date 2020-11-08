<?php
App::uses('FailedAuditsChart', 'Lib/Reports/Chart');
App::uses('Hash', 'Utility');

class ComplianceFailedAuditsChart extends FailedAuditsChart
{
	public function setData($subject)
	{
		$securityServices = [];

		if (!empty($subject->item->CompliancePackage)) {
			foreach ($subject->item->CompliancePackage as $CompliancePackage) {
				if (!empty($CompliancePackage->CompliancePackageItem)) {
					foreach ($CompliancePackage->CompliancePackageItem as $CompliancePackageItem) {
						if (!empty($CompliancePackageItem->ComplianceManagement) && !empty($CompliancePackageItem->ComplianceManagement->SecurityService)) {
							foreach ($CompliancePackageItem->ComplianceManagement->SecurityService as $SecurityService) {
								if (!isset($securityServices[$SecurityService->id])) {
									$securityServices[$SecurityService->id] = $SecurityService;
								}
							}
						}
					}
				}
			}
		}

		$subject->collection = $securityServices;

		parent::setData($subject);
	}
}