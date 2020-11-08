<?php
namespace Suggestion\Package\Process;

class DefaultPackage extends BasePackage {
	public $alias = 'DefaultPackage';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Default');

		$this->data = array(
			'name' => $this->name,
			'description' => __('Default Process')
		);

	}
}
