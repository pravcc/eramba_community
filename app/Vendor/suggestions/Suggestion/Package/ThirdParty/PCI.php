<?php
namespace Suggestion\Package\ThirdParty;

class PCI extends BasePackage {
	public $alias = 'PCI';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('PCI-DSS');

		$this->data = array(
			'name' => __('PCI-DSS'),
			'description' => __('The payments security standard'),
			'third_party_type_id' => 3,
			'legal_id' => '',
			'sponsor_id' => ''
		);
	}
}
