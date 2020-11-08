<?php
class NotificationsBase {
	public $internal;
	public $title;
	public $description;
	public $model;
	public $customFind = false;
	public $conditions = array();
	public $contain = false;
	public $triggerPeriod = false; // false if none, otherwise number as day count or strings: '1 day', '2 weeks', '1 month'...
	public $options = array();
	public $customEmailTemplate = false;
	public $order = 1;

	public $_periodDateFrom = null;
	public $_periodFormatted = null;

	// default notification can be used in all sections, is bound to a model used in multiple system parts/models, is triggered when 	some action happens - for example as afterSave callback when an item is created.
	public $isDefaultType = false;
	public $defaultTypeSettings = array();

	public $isReportType = false;

	// in case this notifications was deprecated or exchanged for a different one, put message here.
	public $deprecated = false;

	/**
	 * Notification constructor to set core variables.
	 * In your notification class use as: parent::__construct();
	 */
	public function __construct($options = array()) {
		if (!empty($options)) {
			if (isset($options['triggerPeriod']) && !empty($options['triggerPeriod'])) {
				$this->triggerPeriod = (int) $options['triggerPeriod'];
			}
		}

		if ($this->triggerPeriod) {
			if (is_string($this->triggerPeriod)) {
				$this->_periodFormatted = $this->triggerPeriod;
			}
			else {
				$this->_periodFormatted = $this->triggerPeriod . ' days';
			}

			$timestamp = strtotime('-' . $this->_periodFormatted, strtotime('now'));
			if ($timestamp) {
				$this->_periodDateFrom = date('Y-m-d H:i:s', $timestamp);
			}
		}
	}

	/**
	 * Parse custom data. Must return booelan - true if item is triggered, false otherwise.
	 */
	public function parseData($data) {
		return !empty($data);
	}

	public function getOrder() {
		$order = $this->order;

		if (isset($this->reminderDays)) {
			$order = $this->reminderDays;
		}

		return $order;
	}
}
