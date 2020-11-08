<?php
App::uses('AwarenessProgramUserRenderProcessor', 'View/Renderer/Processor');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');

class AwarenessProgramIgnoredUserRenderProcessor extends AwarenessProgramUserRenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
		parent::render($output, $subject);
	}

}