<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class SecurityServiceAuditItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$SecurityServiceAudit = ClassRegistry::init('SecurityServiceAudit');
		$SecurityService = ClassRegistry::init('SecurityService');

		$this
			->setSlug('security-service-audit-item-default-report')
			->setReport([
				'model' => 'SecurityServiceAudit',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Control Audit Report: %s</b></h1><p>This is the report for the testing planned for the date %s which started on %s and finished on %s.</p>',
					$SecurityService->getMacroByName('name'),
					$SecurityServiceAudit->getMacroByName('planned_date'),
					$SecurityServiceAudit->getMacroByName('start_date'),
					$SecurityServiceAudit->getMacroByName('end_date')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'SecurityServiceAudit',
				[
					'audit_metric_description', 'audit_success_criteria', 'result_description', 'result'
				],
				__(' ')
			);
	}
}