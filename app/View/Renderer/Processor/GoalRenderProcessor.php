<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('RelatedProjectStatusesTrait', 'View/Renderer/Processor/Trait');

class GoalRenderProcessor extends SectionRenderProcessor
{
	use RelatedProjectStatusesTrait;
}