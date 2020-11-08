<?php
namespace Suggestion\Package\BusinessUnit;
use Suggestion\Package\Legal\SOX;

class Finance extends BasePackage {
	public $alias = 'Finance';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Finance');

		$this->data = array(
			'name' => $this->name,
			'description' => __('The business functions of a finance department typically include planning, organizing, auditing, accounting for and controlling its company finances.'),
			'legal_id' => array( new SOX() ),
			'business_unit_owner_id' => array(ADMIN_ID)
		);

	}
}
