<?php
namespace Suggestion\Package\AssetClassificationType;

class Confidentiality extends BasePackage {
	public $alias = 'Confidentiality';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Confidentiality');

		$this->data = array(
			'name' => $this->name
		);
	}

}
