<?php
namespace Suggestion\Package\SecurityService;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'SecurityService';
	public $requestAction = '/securityServices/add';

	protected function getAuditDate() {
		$date = $this->randomDate();
		$timestamp = strtotime($date);

		return array(
			'day' => date('d', $timestamp),
			'month' => date('m', $timestamp)
		);
	}

	protected function getDefaultOpex() {
		return 0;
	}

	protected function getDefaultCapex() {
		return 0;
	}
}
