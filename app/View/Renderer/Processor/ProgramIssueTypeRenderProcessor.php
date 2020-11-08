<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('FieldsIterator', 'ItemData.Lib');
App::uses('ProgramIssue', 'Model');

class ProgramIssueTypeRenderProcessor extends SectionRenderProcessor
{
	public function type(OutputBuilder $output, $subject)
	{
		$types = ProgramIssue::getInternalTypes() + ProgramIssue::getExternalTypes();

		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);

		foreach ($FieldsIterator as $key => $value) {
			$output->label([$key => $types[$value['raw']]]);
		}
	}
}