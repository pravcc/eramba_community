<?php
namespace Suggestion\Package\ThirdParty;

class ISO27001 extends BasePackage {
	public $alias = 'ISO27001';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('ISO 27001');

		$this->data = array(
			'name' => __('ISO 27001'),
			'description' => __('ISO standard for ISO 27001'),
			'third_party_type_id' => 3,
			'legal_id' => '',
			'sponsor_id' => ''
		);
	}
}
