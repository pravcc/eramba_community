<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('FieldsIterator', 'ItemData.Lib');
App::uses('ProgramIssue', 'Model');

class ProgramIssueRenderProcessor extends SectionRenderProcessor
{
	public function programIssueType(OutputBuilder $output, $subject)
	{
		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);

		foreach ($FieldsIterator as $key => $value) {
			$types = ($value['item']->issue_source == ProgramIssue::SOURCE_INTERNAL) ? getInternalTypes() : getExternalTypes();

			$output->label([$key => $types[$value['raw']]]);
		}
	}
}