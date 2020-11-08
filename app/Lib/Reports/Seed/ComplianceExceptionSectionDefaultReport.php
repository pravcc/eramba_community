<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class ComplianceExceptionSectionDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$ComplianceException = ClassRegistry::init('ComplianceException');

		$this
			->setSlug('compliance-exception-section-default-report')
			->setReport([
				'model' => 'ComplianceException',
				'name' => __('System Report - Section')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_SECTION,
				'name' => __('System Report Template - Section')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__('<h1><b>Compliance Exception Summary</b></h1>')
			)
			->addChartBlock(
				ReportBlock::SIZE_6,
				'ComplianceException',
				1,
				__('<h2><b>Top Ten Exceptions by Compliance Package</b></h2><p>This pie charts shows the top 10 compliance packages by their number of asociated exceptions.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'ComplianceException',
				2,
				__('<h2><b>Exceptions by Duration</b></h2><p>This chart shows exceptions distributed by their duration from start to close date.</p>')
			);
	}
}