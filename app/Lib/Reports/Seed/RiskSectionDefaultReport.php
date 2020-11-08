<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class RiskSectionDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$this
			->setSlug('risk-section-default-report')
			->setReport([
				'model' => 'Risk',
				'name' => __('System Report - Section')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_SECTION,
				'name' => __('System Report Template - Section')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__('<h1><b>Asset Risk Summary</b></h1><p>This report describes general attributes for all Asset Risks in the scope of this GRC program.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'Risk',
				16,
				__('<h2><b>Risk Matrix</b></h2><p>This chart shows risks based on their classification, the matrix includes the description and colour of thresholds.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'Risk',
				13,
				__('<h2><b>Risk Score and Residual over time</b></h2><p>This chart shows the amount of risk and residual over time.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'Risk',
				11,
				__('<h2><b>Risks by Treatment Option</b></h2><p>This chart shows the amount of risks by treatment option.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'Risk',
				12,
				__('<h2><b>Risks by Tags</b></h2><p>This chart shows risks based on their assigned tags.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'Risk',
				18,
				__('<h2><b>Top 20 Risk Owner</b></h2><p>The chart shows the top 20 risk owners.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'Risk',
				19,
				__('<h2><b>Top 20 Risk Stakeholders</b></h2><p>The chart shows the top 20 risk stakeholders.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'Risk',
				20,
				__('<h2><b>Accumulated Risk by Owner</b></h2><p>This chart shows risk score grouped by Risk Owner.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'Risk',
				21,
				__('<h2><b>Accumulated Risk by Stakeholder</b></h2><p>This chart shows risk score grouped by Risk Stakeholder.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'Risk',
				26,
				__('<h2><b>Risks by Status</b></h2><p>This chart shows risks by their associated treatment options status, no that risks can have more than one status and therefore you might have more items in the pie than actual number of risks.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'Risk',
				27,
				__('<h2><b>Top 10 Threats</b></h2><p>The chart shows the top 10 used threats.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'Risk',
				28,
				__('<h2><b>Top 10 Vulnerabilities</b></h2><p>The chart shows the top 10 used vulnerabilities.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'Risk',
				29,
				__('<h2><b>Top 10 Tags</b></h2><p>The chart shows the top 10 used tags.</p>')
			);
	}
}