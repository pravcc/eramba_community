<?php
App::uses('ReportSeed', 'Reports.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlock', 'Reports.Model');

/**
 * Report Seed.
 */
class DataAssetInstanceItemDefaultReport extends ReportSeed
{
	/**
	 * Use construct to set data.
	 */
	public function __construct()
	{
		$DataAssetInstance = ClassRegistry::init('DataAssetInstance');

		$this
			->setSlug('data-asset-instance-item-default-report')
			->setReport([
				'model' => 'DataAssetInstance',
				'name' => __('System Report - Item')
			])
			->setReportTemplate([
				'type' => ReportTemplate::TYPE_ITEM,
				'name' => __('System Report Template - Item')
			])
			->addTextBlock(
				ReportBlock::SIZE_12,
				__(
					'<h1><b>Data Flow Basic Report: %s</b></h1><p>This report describes general attributes for this asset data flow.</p>',
					$DataAssetInstance->getMacroByName('asset_id')
				)
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'DataAsset',
				[
					'data_asset_status_id', 'title', 'BusinessUnit', 'ThirdParty', 'SecurityService', 'SecurityPolicy', 'Project',
				],
				__('<h2><b>Basic Attributes</b></h2><p>The table below displays basic attributes for each data flow.</p>')
			)
			->addChartBlock(
				ReportBlock::SIZE_12,
				'DataAssetInstance',
				1,
				__('<h2><b>Data Flow Tree</b></h2><p>This tree chart shows for a given asset all its stages, flows and mitigation controls, policies, risks and projects.</p>')
			)
			->addTableBlock(
				ReportBlock::SIZE_12,
				'DataAssetSetting',
				[
					'gdpr_enabled', 'driver_for_compliance', 'DataOwner', 'Dpo', 'Processor', 'Controller', 'ControllerRepresentative', 'SupervisoryAuthority'
				],
				__('<h2><b>General GDPR Attributes</b></h2><p>The table below describes basic GDPR settings for this asset.</p>')
			);
	}
}