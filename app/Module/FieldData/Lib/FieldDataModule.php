<?php
App::uses('ModuleBase', 'Lib');
class FieldDataModule extends ModuleBase {
	public $toolbar = false;

	protected $_whitelist = [];

	public function __construct() {
		$this->name = __('Field Data Layer');

		parent::__construct();
	}

}
