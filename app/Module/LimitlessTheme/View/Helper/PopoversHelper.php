<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Inflector', 'Utility');

class PopoversHelper extends AppHelper
{
	public $settings = [];
	public $helpers = ['Html', 'LimitlessTheme.Icons'];

	/**
	 * Render of popover.
	 * 
	 * @param string $content Content where we want to apply popover.
	 * @param array $text Popover text.
	 * @param array $text Popover title.
	 * @param array $options Additional options.
	 * @return string Popover html.
	 */
	public function render($content, $text, $title = null, $options = [])
	{
		$options = array_merge([
			'id' => null,
			'placement' => 'top',
			'trigger' => 'hover',
			'html' => true,
			'element' => 'span',
			'icon' => false,
			'class' => [],
			'size' => null,
			'pointer' => false
		], $options);

		if (!empty($options['icon'])) {
			$icon = ($options['icon'] === true) ? 'info22' :  $options['icon'];
			$content .= ' ' . $this->Icons->render($icon);
		}

		if (!empty($options['pointer'])) {
			$options['class'] = (array) $options['class'];
			$options['class'][] = 'cursor-pointer';
		}

		return $this->Html->tag($options['element'], $content, [
			'data-container' => 'body',
			'data-popup' => 'popover',
			'data-placement' => $options['placement'],
			'data-trigger' => $options['trigger'],
			'data-original-title' => htmlspecialchars($title),
			'data-content' => htmlspecialchars($text),
			'data-html' => $options['html'],
			'data-size' => $options['size'],
			'class' => $options['class'],
			'id' => $options['id'],
			'escape' => false
		]);
	}

	public function __call($name, $args) {
		$options = ['placement' => Inflector::underscore($name)];

		if (count($args) < 2) {
			throw new InternalErrorException('Popover text or content is missing.');
		}

		if (!empty($args[3])) {
			$options += (array) $args[3];
		}

		return $this->render($args[0], $args[1], (isset($args[2])) ? $args[2] : null, $options);
	}
}