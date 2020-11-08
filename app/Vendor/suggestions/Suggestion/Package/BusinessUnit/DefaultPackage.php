<?php
namespace Suggestion\Package\BusinessUnit;
// use Suggestion\Package\Process\DefaultPackage;
use Suggestion\Package\Legal\PersonalDataAct;
use Suggestion\Package\Legal\ConfidentialityAgreements;

class DefaultPackage extends BasePackage {
	public $alias = 'DefaultPackage';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Human Resources');

		$this->data = array(
			'name' => __('Human Resources'),
			'description' => __('HR responsibilities include payroll, benefits, hiring, firing, and keeping up to date with state and federal tax laws.'),
			'legal_id' => array( new PersonalDataAct(), new ConfidentialityAgreements() ),
			'business_unit_owner_id' => array(ADMIN_ID)
		);

	}
}
