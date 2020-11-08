<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class AwarenessProgramItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$AwarenessProgram = ClassRegistry::init('AwarenessProgram');

		$this
			->setSlug('awareness-program-item-default-report')
			->setReport([
				'model' => 'AwarenessProgram',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Awareness Report: %s</b></h1><p>This report describes general attributes for this Awareness Training.</p>',
					$AwarenessProgram->getMacroByName('title')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'AwarenessProgram',
				[
					'description', 'recurrence', 'video', 'questionnaire', 'status', 'active_users'
				],
				__('<h2><b>Awareness Program Attributes</b></h2><p>The table below shows general settings for this Awareness Training.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'AwarenessProgram',
				2,
				__('<h2><b>Compliance Over Time</b></h2><p>This chart shows the number of participants and compliant users for this awareness program.</p>')
			);
	}
}