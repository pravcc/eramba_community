<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class PolicyExceptionSectionDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$PolicyException = ClassRegistry::init('PolicyException');

		$this
			->setSlug('policy-exception-section-default-report')
			->setReport([
				'model' => 'PolicyException',
				'name' => __('System Report - Section')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_SECTION,
				'name' => __('System Report Template - Section')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__('<h1><b>Policy Exception Summary</b></h1>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'PolicyException',
				1,
				__('<h2><b>Top 10 Exceptions by Policy</b></h2><p>This pie charts shows the top 10 policies by their number of asociated exceptions.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'PolicyException',
				2,
				__('<h2><b>Exceptions by Duration</b></h2><p>This chart shows exceptions distributed by their duration from start to close date.</p>')
			);
	}
}