<?php
App::uses('AppHelper', 'View/Helper');

class IconsHelper extends AppHelper
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
	public function render($icon, $options = [])
	{
		$options = array_merge([
			'class' => null
		], $options);

		$class = 'icon-' . $icon;
		if (!is_null($options['class'])) {
			$class .= ' ' . $options['class'];
		}

		$options['class'] = $class;

		return $this->Html->tag('i', false, $options);
	}
}