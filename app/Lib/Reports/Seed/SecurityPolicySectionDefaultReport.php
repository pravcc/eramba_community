<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class SecurityPolicySectionDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$this
			->setSlug('security-policy-section-default-report')
			->setReport([
				'model' => 'SecurityPolicy',
				'name' => __('System Report - Section')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_SECTION,
				'name' => __('System Report Template - Section')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__('<h1><b>Policies Summary</b></h1><p>This report describes general attributes for all policies in the scope of this GRC program.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityPolicy',
				2,
				__('<h2><b>Policies by Mitigation</b></h2><p>This ven diagram shows the proportion on how policies are used against Internal Controls, Asset Risks, Third Party Risks, Business Risks, Compliance and Data Flow Analysis.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityPolicy',
				4,
				__('<h2><b>Policy Reviews Over Time</b></h2><p>This chart shows all policy review records over time: completed, missing and pending. It also shows the quantity based on the size of the circle.</p>')
			);
	}
}