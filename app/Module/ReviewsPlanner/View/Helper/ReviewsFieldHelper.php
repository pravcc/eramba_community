<?php
App::uses('AppHelper', 'View/Helper');

class ReviewsFieldHelper extends AppHelper {
	public $settings = array();
	public $helpers = array('Form', 'Html');

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	public function input($field, $options = []) {
		$isEdit = $this->_View->get('edit');

		return $this->Form->input($field, array(
			'type' => 'text',
			'label' => false,
			'div' => false,
			'class' => 'form-control datepicker',
			'disabled' => !empty($isEdit)
		));
	}

}