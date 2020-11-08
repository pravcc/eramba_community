<?php
namespace Suggestion\Package\Legal;

class PCIDSS extends BasePackage {
	public $alias = 'PCIDSS';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('PCI-DSS');

		$this->data = array(
			'name' => $this->name,
			'description' => __('The PCI standard'),
			'legal_advisor_id' => array(ADMIN_ID)
		);
	}
}
