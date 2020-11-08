<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class CompliancePackageInstanceItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$CompliancePackageInstance = ClassRegistry::init('CompliancePackageInstance');

		$this
			->setSlug('compliance-package-instance-item-default-report')
			->setReport([
				'model' => 'CompliancePackageInstance',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Compliance Analysis Report: %s</b></h1><p>This report describes general attributes for this Compliance Package.</p>',
					$CompliancePackageInstance->getMacroByName('name')
				)
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'CompliancePackageInstance',
				3,
				__('<h2><b>Compliance by Status</b></h2><p>This chart shows for each chapter what is the intended compliance treatment.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'CompliancePackageInstance',
				1,
				__('<h2><b>Compliance Strategy</b></h2><p>This chart shows the treatment status for each chapter on the compliance package.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'CompliancePackageInstance',
				5,
				__('<h2><b>Top 10 Controls that failed the most Audits (by proportion)</b></h2><p>This charts looks at all controls used in a given compliance analysis package and sorts them by those that proportionally failed the most audits.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'CompliancePackageInstance',
				6,
				__('<h2><b>Top 10 Controls that failed the most Audits (by number)</b></h2><p>This charts looks at all controls used in a given compliance analysis package and sorts them by those that failed the most audits. A second bar shows the total number of audits.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'CompliancePackage.CompliancePackageItem.ComplianceManagement',
				[
					'item_id', 'item_name', 'owner_id', 'SecurityService', 'SecurityPolicy', 'Project'
				],
				__('<h2><b>Compliance Package Items</b></h2><p>The table below lists all compliance package items and their treatment settings.</p>')
			);
	}
}