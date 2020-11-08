<?php
App::uses('AppHelper', 'View/Helper');
App::uses('AppModule', 'Lib');

class ModulesHelper extends AppHelper {
	public $helpers = ['Html', 'Ux'];

	public function __construct(View $view, $settings = array()) {

		parent::__construct($view, $settings);
	}

	public function getToolbar() {
		$enabled = AppModule::getEnabledModules();
		$args = func_get_args();

		$ret = '';
		foreach ($enabled as $module) {
			$instance = AppModule::instance($module);
			if ($instance->toolbar !== true) {
				continue;
			}
			
			if ($instance !== false && $instance->getSectionUrl($args[0])) {
				// debug($module);
				$ret .= call_user_func_array([$this->_View->loadHelper($module . '.' . $module), 'getSectionBtn'], $args);
			}
		}

		return $ret;
	}
}
