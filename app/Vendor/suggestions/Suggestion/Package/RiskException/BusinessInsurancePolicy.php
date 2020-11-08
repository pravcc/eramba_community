<?php
namespace Suggestion\Package\RiskException;

class BusinessInsurancePolicy extends BasePackage {
	public $alias = 'BusinessInsurancePolicy';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Business Insurance Policy');

		$this->data = array(
			'title' => $this->name,
			'description' => 'The Risk owner has decided to transfer the impact of the risk on the company insurance policy (worth 8m) citing lack of funding and resources to implement the controls required to properly mitigate the risk.',
			'author_id' => ADMIN_ID,
			'expiration' => date('Y-m-d', strtotime("+1 month")),
			'status' => EXCEPTION_OPEN
		);

	}

}
