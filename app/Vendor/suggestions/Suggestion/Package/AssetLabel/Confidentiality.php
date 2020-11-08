<?php
namespace Suggestion\Package\AssetLabel;

class Confidentiality extends BasePackage {
	public $alias = 'Confidentiality';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Confidential');

		$this->data = array(
			'name' => $this->name,
			'description' => __('The information must be kept confidential to the data owners')
		);
	}
}
