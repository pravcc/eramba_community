<?php
namespace Suggestion\Package\RiskClassificationType;

class Impact extends BasePackage {
	public $alias = 'Impact';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Impact');

		$this->data = array(
			'name' => $this->name
		);
	}

}
