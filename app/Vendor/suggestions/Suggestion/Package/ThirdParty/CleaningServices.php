<?php
namespace Suggestion\Package\ThirdParty;

use Suggestion\Package\Legal\PCIDSS;
use Suggestion\Package\Legal\ContractualAgreements;
use Suggestion\Package\Legal\ConfidentialityAgreements;

class CleaningServices extends BasePackage {
	public $alias = 'CleaningServices';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Cleaning Services');

		$this->data = array(
			'name' => __('Cleaning Services'),
			'description' => __('Third party company that takes care of mantaining things'),
			'third_party_type_id' => 2,
			'legal_id' => array( new ContractualAgreements() ),
			'sponsor_id' => ''
		);
	}
}
