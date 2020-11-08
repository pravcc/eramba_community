<?php
App::uses('ErambaHelper', 'View/Helper');
class ErambaTimeHelper extends ErambaHelper {
	public $helpers = array('Html');
	public $settings = array();
	private $today;
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;

		$this->today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
	}

	/**
	 * Checks if date is expired.
	 */
	public function isDateExpired($date) {
		$this->today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
		if ($date < $this->today) {
			return true;
		}
	
		return false;
	}

	/**
	 * Returns expired label based on date.
	 */
	public function getExpiredByDateLabel($date, $options = array()) {
		$defaults = array(
			'showNotExpiredLabel' => false
		);

		$options = array_merge($defaults, $options);

		if ($this->isDateExpired($date)) {
			return $this->Html->tag('span', __('Expired'), array('class' => 'label label-danger'));
		}
		else {
			if ($options['showNotExpiredLabel'] === true) {
				return $this->Html->tag('span', __('Not Expired'), array('class' => 'label label-success'));
			}
		}

		return false;
	}

	/**
	 * Returns expired label based on expired status.
	 */
	public function getExpiredByStatusLabel($expired, $options = array()) {
		$defaults = array(
			'showNotExpiredLabel' => false
		);

		$options = array_merge($defaults, $options);

		$status = array();
		if ($expired) {
			$status[] = $this->Html->tag('span', __('Expired'), array('class' => 'label label-danger'));
		}
		else {
			if ($options['showNotExpiredLabel'] === true) {
				$status[] = $this->Html->tag('span', __('Not Expired'), array('class' => 'label label-success'));
			}
		}

		return $this->processStatusesGroup($status);
	}

}