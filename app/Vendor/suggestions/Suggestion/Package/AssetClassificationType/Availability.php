<?php
namespace Suggestion\Package\AssetClassificationType;

class Availability extends BasePackage {
	public $alias = 'Availability';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Availability');

		$this->data = array(
			'name' => $this->name
		);
	}

}
