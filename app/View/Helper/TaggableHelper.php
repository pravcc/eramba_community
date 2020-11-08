<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Hash', 'Utility');

class TaggableHelper extends AppHelper {
	public $settings = array();
	public $helpers = ['Html', 'Ux'];

	public $notFoundMsg = null;
	
	public function __construct(View $view, $settings = array()) {
		$this->notFoundMsg = __('No Tags found.');

		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	/**
	 * Check data array for tags.
	 */
	public function hasTags($item) {
		return !empty($item['Tag']) && is_array($item['Tag']);
	}

	/**
	 * Extract array of tags from the item.
	 */
	public function getList($item) {
		if (!$this->hasTags($item)) {
			return false;
		}

		$data = Hash::extract($item, 'Tag.{n}.title');

		$ret = [];
		foreach ($data as $tag) {
			$ret[] = $this->Html->tag('span', $tag, array('class' => 'label label-info'));
		}

		return $ret;
	}

	/**
	 * Main wrapper method to handle how tags are shown.
	 * @param  array $item     Item data array.
	 * @param  array $options  Optionally configure the output.
	 * 
	 * @return mixed           String output to echo in the View, or false on failure.
	 */
	public function showList($item, $options = array()) {
		$options = am([
			'outputCallback' => array($this, 'output'),
			'notFoundCallback' => array($this, 'notFound')
		], $options);

		$data = $this->getList($item);
		if (!empty($data)) {
			if (is_callable($options['outputCallback'])) {
				return call_user_func_array($options['outputCallback'], [$data]);
			}
			
			trigger_error(__('Taggable "outputCallback" is incorrectly defined.'));
			return false;
		}

		if (is_callable($options['notFoundCallback'])) {
			return call_user_func_array($options['notFoundCallback'], []);
		}

		trigger_error(__('Taggable "notFoundCallback" is incorrectly defined.'));
		return false;
	}

	/**
	 * Output list of tags by default.
	 */
	public function output($data) {
		return implode(' ', $data);
	}

	/**
	 * Not found default alert message.
	 */
	public function notFound() {
		return $this->Ux->getAlert($this->notFoundMsg, [
			'type' => 'info'
		]);
	}

	/**
	 * Not found blank message.
	 */
	public function notFoundBlank() {
		return getEmptyValue(null);
	}

	/**
	 * Extract array of tags from the item.
	 */
	public function outputTags($item) {
		$tags = $this->getList($item);

		$output = $this->notFoundBlank();

		if (!empty($tags)) {
			$output = $this->output($tags);
		}

		return $output;
	}

}
