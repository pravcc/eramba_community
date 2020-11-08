<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class RiskExceptionItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$RiskException = ClassRegistry::init('RiskException');

		$this
			->setSlug('risk-exception-item-default-report')
			->setReport([
				'model' => 'RiskException',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Risk Exception Report: %s</b></h1><p>This report describes general attributes for this Risk Exception.</p>',
					$RiskException->getMacroByName('title')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'RiskException',
				[
					'description', 'expiration', 'closure_date', 'Requestor', 'status'
				],
				__('<h2><b>Basic Attributes</b></h2><p>The table below shows general settings for this exception.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'Risk',
				[
					'title', 'description', 'residual_score'
				],
				__('<h2><b>Associated Asset Risks</b></h2><p>The table below shows all associated Asset Risks for this Exception.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'ThirdPartyRisk',
				[
					'title', 'description', 'residual_score'
				],
				__('<h2><b>Associated Third Party Risks</b></h2><p>The table below shows all associated Third Party risk for this exception.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'BusinessContinuity',
				[
					'title', 'description', 'risk_score'
				],
				__('<h2><b>Associated Business Risks</b></h2><p>The table below shows all associated Business Risks for this exception.</p>')
			);
	}
}