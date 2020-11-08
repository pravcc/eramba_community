<?php
namespace Suggestion\Package\RiskClassificationType;

class Likelihood extends BasePackage {
	public $alias = 'Likelihood';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Likelihood');

		$this->data = array(
			'name' => $this->name
		);
	}

}
