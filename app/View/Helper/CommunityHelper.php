<?php
App::uses('AppHelper', 'View/Helper');

class CommunityHelper extends AppHelper {
	public $helpers = ['Html', 'Limitless.Labels'];
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function enterpriseLabel()
	{
		$label = $this->Labels->danger(__('Enterprise'), [
			'class' => 'enterprise-toolbar-label'
		]);

		$wrapper = $this->Html->div('enterprise-toolbar-label-wrapper', $label, [
			'escape' => false
		]);

		return $wrapper;
	}

	public function enterpriseUrl()
	{
		return 'https://www.eramba.org/services';
	}

}
