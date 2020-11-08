<?php
App::uses('AppHelper', 'View/Helper');

class IconHelper extends AppHelper {
	public $helpers = ['Html'];

	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
	}

	/**
	 * Get icon in HTML format.
	 * 
	 * @param  string $alias   Icon.
	 * @param  array  $options TBD
	 * @return string
	 */
	public function icon($alias, $options = []) {
		$options = am(array(), $options);

		$options['class'][] = 'icon-' . $alias;

        return $this->Html->tag('i', false, $options);
	}
	
}
