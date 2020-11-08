<?php
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');

class BaseOutput extends OutputBuilder
{
	protected $_settings = [
 		'templates' => []
	];

	public function render()
	{
		$output = '';

		foreach ($this->_settings['templates'] as $template) {
			$output = $this->fetchContent($template, $output);
		}

		return $output;
	}

	public function template($template) {
		$this->_settings['templates'][] = $template;
	}
}