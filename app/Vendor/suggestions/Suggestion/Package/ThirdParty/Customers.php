<?php
namespace Suggestion\Package\ThirdParty;
use Suggestion\Package\Legal\ContractualAgreements;

class Customers extends BasePackage {
	public $alias = 'Customers';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Customers');

		$this->data = array(
			'name' => __('Customers'),
			'description' => __('The organisation customers'),
			'third_party_type_id' => 1,
			'legal_id' => array(
				new ContractualAgreements()
			),
			'sponsor_id' => ''
		);
	}
}
