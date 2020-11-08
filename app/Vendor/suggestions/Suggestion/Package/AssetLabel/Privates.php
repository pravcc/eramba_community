<?php
namespace Suggestion\Package\AssetLabel;

class Privates extends BasePackage {
	public $alias = 'Privates';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Private');

		$this->data = array(
			'name' => $this->name,
			'description' => __('The information asociated with this asset is deemed private to the organisation')
		);
	}
}
