<?php
namespace Suggestion\Package\RiskException;

class NoBudget extends BasePackage {
	public $alias = 'NoBudget';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Lack of funding');

		$this->data = array(
			'title' => $this->name,
			'description' => 'The board and the directors have stated not enough funding to mitigate certain risks',
			'author_id' => ADMIN_ID,
			'expiration' => date('Y-m-d', strtotime("+1 month")),
			'status' => EXCEPTION_OPEN
		);

	}

}
