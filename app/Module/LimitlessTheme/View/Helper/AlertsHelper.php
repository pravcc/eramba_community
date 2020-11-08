<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Inflector', 'Utility');

class AlertsHelper extends AppHelper
{
	public $settings = [];
	public $helpers = ['Html'];

	/**
	 * Render of any alert.
	 * 
	 * @param string $icon Alert type.
	 * @param array $options Additional options.
	 * @return string Alert html.
	 */
	public function render($text, $options = [])
	{
		$options = array_merge([
			'type' => 'info',
			'class' => [],
			'escape' => false
		], $options);

		$options['class'] = array_merge(
			['label', 'border-left-' . $options['type'], 'label-striped', 'label-custom-alert'],
			(array) $options['class']
		);

		return $this->Html->tag('div', $text, $options);
	}

	public function __call($name, $args) {
		$options = ['type' => Inflector::underscore($name)];

		if (count($args) < 1) {
			throw new InternalErrorException('Alert text is missing.');
		}

		if (!empty($args[1])) {
			$options += (array) $args[1];
		}

		return $this->render($args[0], $options);
	}
}