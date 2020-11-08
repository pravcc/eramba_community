<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class SecurityPolicyItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$SecurityPolicy = ClassRegistry::init('SecurityPolicy');

		$this
			->setSlug('security-policy-item-default-report')
			->setReport([
				'model' => 'SecurityPolicy',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Security Policy Report: %s</b></h1><p>This report general attributes for this policy.</p>',
					$SecurityPolicy->getMacroByName('title')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'SecurityPolicy',
				[
					'short_description', 'published_date', 'version', 'Owner', 'Collaborator'
				],
				__('<h2><b>Basic Attributes</b></h2><p>The table below shows general settings for this policy.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityPolicy',
				6,
				__('<h2><b>Related Risk Items</b></h2><p>This tree chart shows all related risk items linked.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityPolicy',
				5,
				__('<h2><b>Related Compliance Items</b></h2><p>This tree chart shows all related compliance requirements linked to this item.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityPolicy',
				1,
				__('<h2><b>Policies by Mitigation</b></h2><p>This ven diagram shows the proportion on how policies are used against Internal Controls, Asset Risks, Third Party Risks, Business Risks, Compliance and Data Flow Analysis.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'SecurityPolicyReview',
				[
					'planned_date', 'actual_date', 'description', 'version', 'use_attachments'
				],
				__('<h2><b>Document Reviews</b></h2><p>The table below shows a list of all reviews for this document.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'SecurityPolicy',
				3,
				__('<h2><b>Policy Reviews Over Time</b></h2><p>This chart shows all policy review records over time: completed, missing and pending. It also shows the quantity based on the size of the circle.</p>')
			);
	}
}