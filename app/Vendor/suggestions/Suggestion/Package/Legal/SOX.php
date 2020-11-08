<?php
namespace Suggestion\Package\Legal;

class SOX extends BasePackage {
	public $alias = 'SOX';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('SOX');
		$this->description = __('Sarbanes Oxley');

		$this->data = array(
			'name' => $this->name,
			'description' => __('Sarbanes-Oxley regulations'),
			'legal_advisor_id' => array(ADMIN_ID),
			'risk_magnifier' => (10)
		);
	}
}
