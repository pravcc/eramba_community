<?php
App::uses('CronAppModel', 'Cron.Model');

class CronTask extends CronAppModel {
	public $belongsTo = [
		'Cron'
	];

	public static function statuses($value = null) {
		$options = array(
			self::STATUS_SUCCESS => __('Success'),
			self::STATUS_ERROR => __('Error'),
			self::STATUS_PENDING => __('Pending'),
		);
		return parent::enum($value, $options);
	}
	const STATUS_SUCCESS = 1;
	const STATUS_ERROR = 2;
	const STATUS_PENDING = 0;
}
