<?php
namespace Suggestion\Package\BusinessUnit;
use Suggestion\Package\Legal\ISO27001;
use Suggestion\Package\Legal\PCIDSS;

class CallCenter extends BasePackage {
	public $alias = 'CallCenter';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Call Center');

		$this->data = array(
			'name' => $this->name,
			'description' => __('The team of people tha receives phone calls in order to process payments to our clients'),
			'legal_id' => array( new ISO27001(), new PCIDSS() ),
			'business_unit_owner_id' => array(ADMIN_ID)
		);

	}
}
