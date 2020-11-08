<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class ComplianceAnalysisFindingItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$ComplianceAnalysisFinding = ClassRegistry::init('ComplianceAnalysisFinding');

		$this
			->setSlug('compliance-analysis-finding-item-default-report')
			->setReport([
				'model' => 'ComplianceAnalysisFinding',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Compliance Analysis Finding Report: %s</b></h1><p>This report describes general attributes for this Compliance Audit Finding.</p>',
					$ComplianceAnalysisFinding->getMacroByName('title')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'ComplianceAnalysisFinding',
				[
					'description', 'due_date', 'Owner', 'Collaborator', 'status'
				],
				__('<h2><b>Basic Attributes</b></h2><p>The table below shows general settings for this Finding.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'ComplianceManagement',
				[
					'package_id', 'package_name', 'item_id', 'item_name', 'item_description'
				],
				__('<h2><b>Compliance Analysis Item</b></h2><p>The table below shows the items affected by this Audit finding.</p>')
			);
	}
}