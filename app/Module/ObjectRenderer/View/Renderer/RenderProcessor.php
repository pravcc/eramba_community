<?php
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');

class RenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
	}

	// protected function _dispatchRender($output, $subject)
	// {
	// 	if (!empty($subject->field)) {
	// 		$fn = Inflector::variable($subject->field->getFieldName());

	// 		if (method_exists($this, $fn)) {
	// 			call_user_func([$this, $fn], $output, $subject);
	// 		}
	// 	}
	// }
}