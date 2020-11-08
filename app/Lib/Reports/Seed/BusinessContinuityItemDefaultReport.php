<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class BusinessContinuityItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$BusinessContinuity = ClassRegistry::init('BusinessContinuity');

		$this
			->setSlug('business-continuity-item-default-report')
			->setReport([
				'model' => 'BusinessContinuity',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Business Risk Report: %s</b></h1><p>This report describes general attributes for this Business Risk.</p>',
					$BusinessContinuity->getMacroByName('title')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'BusinessContinuity',
				[
					'description', 'BusinessUnit', 'Process', 'Owner', 'Stakeholder', 'risk_score', 'residual_score'
				],
				__('<h2><b>Basic Attributes</b></h2><p>The table below shows general settings for this Risk.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'BusinessContinuity',
				17,
				__('<h2><b>Risk Matrix (Thresholds)</b></h2><p>This chart shows risks based on their classification, the matrix includes the description and colour of thresholds.</p>')
			);
	}
}