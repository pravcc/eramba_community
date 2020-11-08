<?php
namespace Suggestion\Package\BusinessUnit;
use Suggestion\Package\Legal\ISO27001;
use Suggestion\Package\Legal\PCIDSS;

class It extends BasePackage {
	public $alias = 'It';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('IT');

		$this->data = array(
			'name' => $this->name,
			'description' => __('Technical department tasked with the design, implementation and operation of Information Technology equipment and applications'),
			'legal_id' => array( new ISO27001(), new PCIDSS() ),
			'business_unit_owner_id' => array(ADMIN_ID)
		);

	}
}
