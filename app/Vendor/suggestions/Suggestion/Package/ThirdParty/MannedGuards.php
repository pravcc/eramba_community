<?php
namespace Suggestion\Package\ThirdParty;

class MannedGuards extends BasePackage {
	public $alias = 'MannedGuards';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Manned Guards');

		$this->data = array(
			'name' => __('Manned Guards'),
			'description' => __('Contractors guarding the company'),
			'third_party_type_id' => 1,
			'legal_id' => '',
			'sponsor_id' => ''
		);
	}
}
