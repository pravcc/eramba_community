<?php
namespace Suggestion\Package\RiskException;

class NoRiskForOwner extends BasePackage {
	public $alias = 'NoRiskForOwner';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Risk owner accepts Risk');

		$this->data = array(
			'title' => $this->name,
			'description' => 'Despite the the score provided by the risk model to be higher than the appettie, the risk owner asociated with this risk has not agreed with the calculation and decided to accept the risk as is',
			'author_id' => ADMIN_ID,
			'expiration' => date('Y-m-d', strtotime("+1 month")),
			'status' => EXCEPTION_OPEN
		);

	}

}
