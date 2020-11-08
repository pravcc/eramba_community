<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');

class AttachmentRenderProcessor extends SectionRenderProcessor
{
	public function name(OutputBuilder $output, $subject)
	{
		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);

        foreach ($FieldsIterator as $key => $value) {
        	$date = date('Y-m-d', strtotime($value['item']->created));
        	$attachment = "{$value['item']->User->full_name} ({$date}) <br>{$value['item']->name}";

        	$output->label([
				$key => $attachment
			]);
		}
	}
}
