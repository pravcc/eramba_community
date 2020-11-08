<?php
namespace Suggestion\Package\ThirdParty;

class DefaultPackage extends BasePackage {
	public $alias = 'DefaultPackage';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Default');

		$this->data = array(
			'name' => $this->name,
			'description' => '',
			'third_party_type_id' => 1,
			'legal_id' => '',
			'sponsor_id' => ''
		);

	}

}
