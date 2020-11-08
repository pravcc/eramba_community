<?php
class Asset_Expiration_Base extends NotificationsBase {
	public $internal = 'asset_expiration';
	public $model = 'Asset';
	protected $reminderDays = null;

	public function __construct($options = array()) {
		parent::__construct($options);

		if ($this->reminderDays === null) {
			return false;
		}

		$days = $this->reminderDays;

		// always positive number
		$absReminder = abs($days);
		$daysLabel = sprintf(__n('%s day', '%s days', $absReminder), $absReminder);
		
		if ($days < 0) {
			$this->title = __('Asset Upcoming Review (-%s)', $daysLabel);
			$this->description = __('Notifies %s before an Asset Review begins', $daysLabel);

			$this->conditions = array(
				$this->model . '.review' => date('Y-m-d', strtotime('+' . $absReminder . ' days'))
			);
		}
		else {
			$this->title = __('Asset Review (+%s)', $daysLabel);
			$this->description = __('Notifies %s after an Asset Review begins', $daysLabel);

			$this->conditions = array(
				$this->model . '.review' => date('Y-m-d', strtotime('-' . $absReminder . ' days'))
			);
		}
	}
}
