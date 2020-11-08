<?php
namespace Suggestion\Package\Legal;

class ISO27001 extends BasePackage {
	public $alias = 'ISO27001';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('ISO/IEC 27001:2013');

		$this->data = array(
			'name' => $this->name,
			'description' => __('The ISO standard'),
			'legal_advisor_id' => array(ADMIN_ID)
		);
	}
}
