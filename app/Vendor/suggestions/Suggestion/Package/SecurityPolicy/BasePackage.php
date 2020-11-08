<?php
namespace Suggestion\Package\SecurityPolicy;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'SecurityPolicy';
	public $requestAction = '/securityPolicies/add';

	// path to documents
	protected $documentsPath = 'Documents';

	/**
	 * Reads html document file (.ctp) for current security policy.
	 * 
	 * @return mixed  Html content if success, false otherwise.
	 */
	protected function readDocument() {
		$file = new \File(dirname(__FILE__) . DS . $this->documentsPath . DS . $this->alias . '.ctp');
		if ($file->exists()) {
			$file->open();
			return $file->read();
		}

		return false;
	}
}
