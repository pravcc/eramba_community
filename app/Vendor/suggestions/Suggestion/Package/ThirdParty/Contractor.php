<?php
namespace Suggestion\Package\ThirdParty;

use Suggestion\Package\Legal\PCIDSS;
use Suggestion\Package\Legal\ContractualAgreements;
use Suggestion\Package\Legal\ConfidentialityAgreements;

class Contractor extends BasePackage {
	public $alias = 'Contractor';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Software Contractors');

		$this->data = array(
			'name' => __('Software Contractors'),
			'description' => __('They provide the software development and mantainance required by the payment application'),
			'third_party_type_id' => 2,
			'legal_id' => array( new ISO27001(), new ConfidentialityAgreements(), new ContractualAgreements() ),
			'sponsor_id' => ''
		);
	}
}
