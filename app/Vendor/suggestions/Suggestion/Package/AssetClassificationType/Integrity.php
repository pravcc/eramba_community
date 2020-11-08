<?php
namespace Suggestion\Package\AssetClassificationType;

class Integrity extends BasePackage {
	public $alias = 'Integrity';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Integrity');

		$this->data = array(
			'name' => $this->name
		);
	}

}
