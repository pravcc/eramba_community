<?php
App::uses('ErambaHelper', 'View/Helper');
class ComplianceFindingsHelper extends ErambaHelper {
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		$this->helpers[] = 'ErambaTime';

		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	public function getStatuses($item) {
		$statuses = array();
		$statuses = array_merge($statuses, $this->ErambaTime->getExpiredByStatusLabel($item['expired']));
		
		return $this->processStatuses($statuses);
	}

}
