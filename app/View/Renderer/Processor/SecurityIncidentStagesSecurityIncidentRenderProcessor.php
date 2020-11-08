<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');

class SecurityIncidentStagesSecurityIncidentRenderProcessor extends SectionRenderProcessor
{
	public function stageName(OutputBuilder $output, $subject)
	{
		$output->label([
			$output->getKey($subject->item, $subject->field) => $subject->item->SecurityIncidentStage->name
		]);
	}

	public function stageDescription(OutputBuilder $output, $subject)
	{
		$output->label([
			$output->getKey($subject->item, $subject->field) => $subject->item->SecurityIncidentStage->description
		]);
	}
}