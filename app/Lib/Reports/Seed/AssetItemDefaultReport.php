<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class AssetItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$Asset = ClassRegistry::init('Asset');

		$this
			->setSlug('asset-item-default-report')
			->setReport([
				'model' => 'Asset',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Asset Report: %s</b></h1><p>This report describes general attributes for this asset.</p>',
					$Asset->getMacroByName('name')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'Asset',
				[
					'description', 'BusinessUnit', 'Legal', 'asset_media_type_id'
				],
				__('<h2><b>Basic Attributes</b></h2><p>The table below shows general settings for this asset.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'Asset',
				1,
				__('<h2><b>Asset and related Objects</b></h2><p>This tree shows the asset and its associated risks, compliance packages, incidents and account reviews.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'Review',
				[
					'planned_date', 'actual_date', 'description', 'completed'
				],
				__('<h2><b>Asset Reviews</b></h2><p>The table below shows a full list of reviews for this asset.</p>')
			);
	}
}