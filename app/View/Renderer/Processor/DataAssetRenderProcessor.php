<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('DataAsset', 'Model');
App::uses('RelatedProjectStatusesTrait', 'View/Renderer/Processor/Trait');
App::uses('FieldsIterator', 'ItemData.Lib');

class DataAssetRenderProcessor extends SectionRenderProcessor
{
	use RelatedProjectStatusesTrait;
	
    public function dataAssetStatusId(OutputBuilder $output, $subject)
    {
    	$FieldsIterator = new FieldsIterator($subject->item, $subject->field);

    	foreach ($FieldsIterator as $key => $value) {
			$output->label([$key => DataAsset::statuses($value['item']->data_asset_status_id)]);
		}
    }
}