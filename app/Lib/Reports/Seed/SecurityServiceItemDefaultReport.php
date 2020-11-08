<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class SecurityServiceItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$SecurityService = ClassRegistry::init('SecurityService');

		$this
			->setSlug('security-service-item-default-report')
			->setReport([
				'model' => 'SecurityService',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Internal Control Report: %s</b></h1><p>This report describes general attributes for this control.</p>',
					$SecurityService->getMacroByName('name')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'SecurityService',
				[
					'objective', 'ServiceOwner', 'Collaborator', 'SecurityPolicy'
				],
				__('<h2><b>Basic Attributes</b></h2><p>The table below shows general settings for this control.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityService',
				9,
				__('<h2><b>Related Policy Items</b></h2><p>This tree chart shows all related policies linked to this item.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityService',
				8,
				__('<h2><b>Related Risk Items</b></h2><p>This tree chart shows all related risk items linked.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityService',
				7,
				__('<h2><b>Related Compliance Items</b></h2><p>This tree chart shows all related compliance requirements linked to this item.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityService',
				3,
				__('<h2><b>Controls by Mitigation</b></h2><p>This ven diagram shows the proportion on how controls are used against Asset Risks, Third Party Risks, Business Risks, Compliance and Data Flow Analysis.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'SecurityService',
				[
					'audit_metric_description', 'audit_success_criteria', 'AuditEvidenceOwner', 'AuditOwner'
				],
				__('<h2><b>Audit Attributes</b></h2><p>The table below shows the audit methodology and stakeholders involved in submitting evidence and analysing it.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'SecurityService',
				12,
				__('<h2><b>Audits by Result (current calendar year)</b></h2><p>This chart shows the proportion of pass, failed and missing audits for this current year.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'SecurityService',
				14,
				__('<h2><b>Audits by Result (past calendar year)</b></h2><p>This chart shows the proportion of pass, failed and missing audits for past year.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityService',
				5,
				__('<h2><b>Audits Results Over Time</b></h2><p>This chart shows all audit records over time which ones failed, pass, are missing or are scheduled in the future. It also shows the quantity based on the size of the circle.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'SecurityServiceAudit',
				[
					'audit_metric_description', 'audit_success_criteria', 'result', 'result_description', 'planned_date', 'end_date'
				],
				__('<h2><b>Audits for this Control</b></h2><p>The table below shows a list of all audit records for this control.</p>')
			);
	}
}