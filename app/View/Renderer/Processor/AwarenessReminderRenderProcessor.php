<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');

class AwarenessReminderRenderProcessor extends SectionRenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
		parent::render($output, $subject);
	}

	public function demo(OutputBuilder $output, $subject)
	{
		$model = $subject->field->getModelName();
		$itemKey = $output->getKey($subject->item, $subject->field);

		if ($subject->item->demo) {
			$label = __('Yes');
		}
		else {
			$label = __('No');
		}

		$output->label([
			$itemKey => $label
		]);
		// $output->itemTemplate([
		// 	$itemKey => $link
		// ]);
	}

}