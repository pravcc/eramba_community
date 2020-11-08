<?php
namespace Suggestion\Package\Goal;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'Goal';
	public $requestAction = '/goals/add';

	protected function getAuditDate() {
		$date = $this->randomDate();
		$timestamp = strtotime($date);

		return array(
			'day' => date('d', $timestamp),
			'month' => date('m', $timestamp)
		);
	}
}
