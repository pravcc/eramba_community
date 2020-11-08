<?php
namespace Suggestion\Package\Legal;

class ContractualAgreements extends BasePackage {
	public $alias = 'ContractualAgreements';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Contractual Agreements');

		$this->data = array(
			'name' => $this->name,
			'description' => __("Any contractual agreement signed in between the organisation and it's customers"),
			'legal_advisor_id' => array(ADMIN_ID)
		);
	}
}
