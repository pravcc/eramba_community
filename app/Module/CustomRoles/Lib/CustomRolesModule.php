<?php
/**
 * @package       CustomRoles.Lib
 */

App::uses('ModuleBase', 'Lib');
App::uses('AppModule', 'Lib');

//share class
class CustomRolesModule extends ModuleBase {
	public $toolbar = false;

	public function __construct() {
		$this->name = __('CustomRoles');

		// use this feature together with workflows only
		// $this->_whitelist = AppModule::instance('Workflows')->whitelist();

		parent::__construct();
	}

}
