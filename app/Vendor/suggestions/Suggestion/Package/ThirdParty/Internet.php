<?php
namespace Suggestion\Package\ThirdParty;

use Suggestion\Package\Legal\ContractualAgreements;

class Internet extends BasePackage {
	public $alias = 'Internet';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Internet Provider');

		$this->data = array(
			'name' => __('Internet Provider'),
			'description' => __('The internet provider is key on this business as its responsible for the transmission of data required to process payments and receive phone calls from clients'),
			'third_party_type_id' => 2,
			'legal_id' => array( new ContractualAgreements() ),
			'sponsor_id' => ''
		);
	}
}
