<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class SecurityServiceSectionDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$this
			->setSlug('security-service-section-default-report')
			->setReport([
				'model' => 'SecurityService',
				'name' => __('System Report - Section')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_SECTION,
				'name' => __('System Report Template - Section')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__('<h1><b>Internal Controls Summary</b></h1><p>This report describes general attributes for all controls in the scope of this GRC program.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'SecurityService',
				4,
				__('<h2><b>Controls by Mitigation</b></h2><p>This ven diagram shows the proportion on how controls are used against Asset Risks, Third Party Risks, Business Risks, Compliance and Data Flow Analysis.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'SecurityService',
				2,
				__('<h2><b>Controls by Audit Results</b></h2><p>This pie chart shows the proportion on how controls against their testing results.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'SecurityService',
				13,
				__('<h2><b>Audits by Result (current calendar year)</b></h2><p>This chart shows the proportion of pass, failed and missing audits for this current year.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'SecurityService',
				15,
				__('<h2><b>Audits by Result (past calendar year)</b></h2><p>This chart shows the proportion of pass, failed and missing audits for past year.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityService',
				6,
				__('<h2><b>Audits Results Over Time</b></h2><p>This chart shows all audit records over time which ones failed, pass, are missing or are scheduled in the future. It also shows the quantity based on the size of the circle.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityService',
				10,
				__('<h2><b>Top 10 Fail Controls by Testing (by proportion)</b></h2><p>This chart shows the top ten controls for the last calendar year that failed the largest proportion of audits.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityService',
				11,
				__('<h2><b>Top 10 Fail Controls by Testing (by counter)</b></h2><p>This chart shows the top ten controls for the last calendar year based on the total number of failed audits. A second bar shows the total number of audits.</p>')
			);
	}
}