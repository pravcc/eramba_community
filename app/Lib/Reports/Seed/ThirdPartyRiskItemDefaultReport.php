<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class ThirdPartyRiskItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$ThirdPartyRisk = ClassRegistry::init('ThirdPartyRisk');

		$this
			->setSlug('third-party-risk-item-default-report')
			->setReport([
				'model' => 'ThirdPartyRisk',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Third Party Risk Report: %s</b></h1><p>This report describes general attributes for this Third Party Risk.</p>',
					$ThirdPartyRisk->getMacroByName('title')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'ThirdPartyRisk',
				[
					'description', 'Asset', 'ThirdParty', 'Owner', 'Stakeholder', 'risk_score', 'residual_score'
				],
				__('<h2><b>Basic Attributes</b></h2><p>The table below shows general settings for this Third Party Risk.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'ThirdPartyRisk',
				3,
				__('<h2><b>Risks and related Objects</b></h2><p>This tree shows the risks and its associated assets, third parties, vulnerabilities, threats, controls, policies and exceptions.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'ThirdPartyRisk',
				[
					'risk_mitigation_strategy_id', 'SecurityService', 'SecurityPolicyTreatment', 'Project', 'RiskException'
				],
				__('<h2><b>Risk Treatment</b></h2><p>The table below shows the treatment strategy for this risk.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'ThirdPartyRisk',
				17,
				__('<h2><b>Risk Matrix (Thresholds)</b></h2><p>This chart shows risks based on their classification, the matrix includes the description and colour of thresholds.</p>')
			);
	}
}