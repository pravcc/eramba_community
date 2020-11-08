<?php
App::uses('RenderProcessor', 'ObjectRenderer.View/Renderer');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');
App::uses('FieldsIterator', 'ItemData.Lib');

class TextRenderProcessor extends RenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
		$output->itemSeparator = ', ';
		$output->itemChunkSize = 1;

		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);

		foreach ($FieldsIterator as $key => $value) {
			$output->label([$key => $value['nice']]);
		}
	}
}