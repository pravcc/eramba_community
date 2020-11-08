<?php
App::uses('AppHelper', 'View/Helper');

class LabelsHelper extends AppHelper
{
	public $settings = [];
	public $helpers = ['Html'];

	/**
	 * Render any icon.
	 * 
	 * @param  string $icon    Icon name
	 * @param  array  $options Additional options for the icon.
	 * @return string          Rendered icon
	 */
	public function render($text, $options = [])
	{
		$options = array_merge([
			'type' => 'default',
			'class' => null
		], $options);

		$class = ['label', 'label-' . $options['type']];
		if (!is_null($options['class'])) {
			$options['class'] = (array) $options['class'];
			$class = array_merge($class, $options['class']);
		}

		return $this->Html->tag('span', $text, [
			'class' => $class,
			'escape' => false
		]);
	}

	public function __call($name, $args) {
		$options = ['type' => Inflector::underscore($name)];

		if (count($args) < 1) {
			throw new InternalErrorException('Label text missing.');
		}

		if (!empty($args[1])) {
			$options += (array)$args[1];
		}

		return $this->render($args[0], $options);
	}
}