<?php
namespace Suggestion\Package\Legal;

class ConfidentialityAgreements extends BasePackage {
	public $alias = 'ConfidentialityAgreements';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Confidentiality Agreements');
		$this->description = __('Non Disclosure Agreements type of contract');

		$this->data = array(
			'name' => $this->name,
			'description' => __('Any confidentiality agreements signed by the organisation'),
			'legal_advisor_id' => array(ADMIN_ID)
		);
	}
}
