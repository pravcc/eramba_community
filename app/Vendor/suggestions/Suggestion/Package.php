<?php
namespace Suggestion;

class Package {
	public $alias = null;
	public $className = null;
	public $name = null;
	public $description = null;
	public $model = null;
	public $requestAction = null;
	public $data;

	// always re-use existing suggestion without additional checks.
	public $forceExistingItem = false;

	public function __construct($options = array()) {
		$this->className = get_class($this);
	}

	/**
	 * Generate random date this year.
	 */
	protected function randomDate() {
		$min_epoch = strtotime("now");
		$year = date('Y', $min_epoch);

		$days = date('t', strtotime('December'));
		$max_date = $year . '-12-' . $days;
		$max_epoch = strtotime($max_date);

		$rand_epoch = rand($min_epoch, $max_epoch);

		return date('Y-m-d H:i:s', $rand_epoch);
	}

	protected function oneYear() {
		return date('Y-m-d', strtotime("+1 year"));
	}

	protected function now() {
		return date('Y-m-d', strtotime("now"));
	}
}