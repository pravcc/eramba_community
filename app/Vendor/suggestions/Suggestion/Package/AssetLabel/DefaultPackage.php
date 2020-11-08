<?php
namespace Suggestion\Package\AssetLabel;

class DefaultPackage extends BasePackage {
	public $alias = 'DefaultPackage';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Default Package');

		$this->data = array(
			'name' => $this->name,
			'description' => __('...')
		);

	}
}
