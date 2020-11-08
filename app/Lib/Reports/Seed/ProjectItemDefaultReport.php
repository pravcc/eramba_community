<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class ProjectItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$Project = ClassRegistry::init('Project');

		$this
			->setSlug('project-item-default-report')
			->setReport([
				'model' => 'Project',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Project Report: %s</b></h1><p>This report describes general attributes for this Project.</p>',
					$Project->getMacroByName('title')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'Project',
				[
					'goal', 'start', 'deadline', 'plan_budget', 'Owner', 'project_status_id'
				],
				__('<h2><b>Basic Project Attributes</b></h2><p>The table below shows general settings for this Project.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'Project',
				1,
				__('<h2><b>Project Relationships</b></h2><p>This chart shows what GRC elements are associated with this project.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'ProjectAchievement',
				[
					'task_order', 'description', 'date', 'completion', 'TaskOwner'
				],
				__('<h2><b>Project Task</b></h2><p>The table below shows the tasks for this project.</p>')
			);
	}
}