<?php
App::uses('ErambaHelper', 'View/Helper');
class ServiceContractsHelper extends ErambaHelper {
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		$this->helpers[] = 'ErambaTime';
		$this->helpers[] = 'NotificationSystem';

		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	public function getStatusArr($item, $allow = '*') {
		$item = $this->processItemArray($item, 'ServiceContract');
		$statuses = array();

		if ($this->getAllowCond($allow, 'expired') && $item['ServiceContract']['expired'] == SERVICE_CONTRACT_EXPIRED) {
			$statuses[$this->getStatusKey('expired')] = array(
				'label' => __('Contract Expired'),
				'type' => 'danger'
			);
		}

		return $statuses;
	}

	public function getStatuses($item, $options = array()) {
		$options = $this->processStatusOptions($options);
		$statuses = $this->getStatusArr($item, $options['allow']);

		return $this->styleStatuses($statuses, $options);
	}

}
