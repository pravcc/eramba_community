<?php
/**
 * Exception class for CRON jobs.
 *
 * @package       Cron.Error
 */
class CronException extends CakeException {

	public function __construct($message, $code = 500) {
		$this->suffix = __('If you are an enterprise customer, please contact support@eramba.org with a screenshot of this error and the error log (System / Settings / Error Log) download.');

		CakeLog::write('Cron', $message);

		parent::__construct($message, $code);
	}

	public function getFullMessage() {
		return $this->message . '<br /><br />' . $this->suffix;
	}
}