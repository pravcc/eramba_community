<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('Portal', 'Model');
App::uses('FieldsIterator', 'ItemData.Lib');

class UserSystemLogRenderProcessor extends SectionRenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
		parent::render($output, $subject);
	}

	public function subForeignKey(OutputBuilder $output, $subject)
	{
		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);

		foreach ($FieldsIterator as $key => $value) {
			$output->label([
				$key => Portal::portals()[$subject->item->sub_foreign_key]
			]);
		}
	}
}