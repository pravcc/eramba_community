<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class RiskItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$Risk = ClassRegistry::init('Risk');

		$this
			->setSlug('risk-item-default-report')
			->setReport([
				'model' => 'Risk',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Asset Risk Report: %s</b></h1><p>This report describes general attributes for this Risk.</p>',
					$Risk->getMacroByName('title')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'Risk',
				[
					'description', 'Asset', 'Owner', 'Stakeholder', 'risk_score', 'residual_score'
				],
				__('<h2><b>Basic Attributes</b></h2><p>The table below shows general settings for this Risk.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'Review',
				[
					'planned_date', 'actual_date', 'description', 'description', 'user_id'
				],
				__('<h2><b>Review</b></h2><p>The table below shows all reviews for this Risk.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'Risk',
				2,
				__('<h2><b>Risks and related Objects</b></h2><p>This tree shows the risks and its associated assets, third parties, vulnerabilities, threats, controls, policies and exceptions.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'Risk',
				[
					'risk_mitigation_strategy_id', 'SecurityService', 'SecurityPolicyTreatment', 'Project', 'RiskException'
				],
				__('<h2><b>Risk Treatment</b></h2><p>The table below shows the risk treatment strategy for this risk.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'Risk',
				17,
				__('<h2><b>Risk Matrix (Thresholds)</b></h2><p>This chart shows risks based on their classification, the matrix includes the description and colour of thresholds.</p>')
			);
	}
}