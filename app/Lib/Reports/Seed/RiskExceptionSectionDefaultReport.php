<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class RiskExceptionSectionDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$RiskException = ClassRegistry::init('RiskException');

		$this
			->setSlug('risk-exception-section-default-report')
			->setReport([
				'model' => 'RiskException',
				'name' => __('System Report - Section')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_SECTION,
				'name' => __('System Report Template - Section')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__('<h1><b>Risk Exception Summary</b></h1>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'RiskException',
				1,
				__('<h2><b>Top 10 Exceptions by Risk</b></h2><p>This pie charts shows the top 10 risks by their number of asociated exceptions.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'RiskException',
				2,
				__('<h2><b>Exceptions by Duration</b></h2><p>This chart shows exceptions distributed by their duration from start to close date.</p>')
			);
	}
}