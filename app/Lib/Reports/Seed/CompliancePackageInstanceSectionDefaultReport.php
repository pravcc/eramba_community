<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class CompliancePackageInstanceSectionDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$this
			->setSlug('compliance-package-instance-section-default-report')
			->setReport([
				'model' => 'CompliancePackageInstance',
				'name' => __('System Report - Section')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_SECTION,
				'name' => __('System Report Template - Section')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__('<h1><b>Compliance Analysis Summary</b></h1><p>This report describes general attributes for all Compliance Packages in the scope of this GRC program.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'CompliancePackageInstance',
				4,
				__('<h2><b>Compliance by Status</b></h2><p>This chart shows for each compliance package what is the intended compliance treatment</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'CompliancePackageInstance',
				2,
				__('<h2><b>Compliance Strategy</b></h2><p>This chart shows the treatment status for each compliance package.</p>')
			);
	}
}