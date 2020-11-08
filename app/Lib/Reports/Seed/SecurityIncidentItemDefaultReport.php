<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class SecurityIncidentItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$SecurityIncident = ClassRegistry::init('SecurityIncident');

		$this
			->setSlug('security-incident-item-default-report')
			->setReport([
				'model' => 'SecurityIncident',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Incident Report: %s</b></h1><p>This report describes general attributes for this Incident.</p>',
					$SecurityIncident->getMacroByName('title')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'SecurityIncident',
				[
					'type', 'description', 'open_date', 'closure_date', 'reporter', 'victim'
				],
				__('<h2><b>Basic Attributes</b></h2><p>The table below shows general settings for this Incident.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityIncident',
				1,
				__('<h2><b>Incident Relationships</b></h2><p>This chart shows what GRC elements are associated with this incident.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'SecurityIncidentStagesSecurityIncident',
				[
					'stage_name', 'stage_description', 'status'
				],
				__('<h2><b>Stage Attributes</b></h2><p>The table below shows stages and status for this incident.</p>')
			);
	}
}