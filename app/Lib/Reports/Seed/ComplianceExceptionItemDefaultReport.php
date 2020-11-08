<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class ComplianceExceptionItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$ComplianceException = ClassRegistry::init('ComplianceException');

		$this
			->setSlug('compliance-exception-item-default-report')
			->setReport([
				'model' => 'ComplianceException',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Compliance Exception Report: %s</b></h1><p>This report describes general attributes for this exception.</p>',
					$ComplianceException->getMacroByName('title')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'ComplianceException',
				[
					'description', 'expiration', 'closure_date', 'Requestor', 'status'
				],
				__('<h2><b>Basic Attributes</b></h2><p>The table below shows general settings for this exception.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'ComplianceManagement',
				[
					'item_id', 'item_name', 'item_description'
				],
				__('<h2><b>Associated Compliance Items</b></h2><p>The table below shows all compliance items related to this exception.</p>')
			);
	}
}