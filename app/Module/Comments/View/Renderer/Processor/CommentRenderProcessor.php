<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');

class CommentRenderProcessor extends SectionRenderProcessor
{
	public function message(OutputBuilder $output, $subject)
	{
		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);

        foreach ($FieldsIterator as $key => $value) {
        	$date = date('Y-m-d', strtotime($value['item']->created));
        	$message = "{$value['item']->User->full_name} ({$date}) <br>{$value['item']->message}";

        	$output->label([
				$key => $message
			]);
		}
	}
}
