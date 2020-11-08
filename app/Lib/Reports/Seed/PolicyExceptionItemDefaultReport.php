<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class PolicyExceptionItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$PolicyException = ClassRegistry::init('PolicyException');

		$this
			->setSlug('policy-exception-item-default-report')
			->setReport([
				'model' => 'PolicyException',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Policy Exception Report: %s</b></h1><p>This report describes general attributes for this policy exception.</p>',
					$PolicyException->getMacroByName('title')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'PolicyException',
				[
					'description', 'expiration', 'closure_date', 'Requestor', 'status'
				],
				__('<h2><b>Basic Attributes</b></h2><p>The table below shows general settings for this policy Exception.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'SecurityPolicy',
				[
					'index', 'version', 'published_date', 'Owner'
				],
				__('<h2><b>Associated Policies with this Exception</b></h2><p>The table below shows all policies asociated with this exception.</p>')
			);
	}
}