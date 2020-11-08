<?php
App::uses('AppHelper', 'View/Helper');

/**
 * Dispatcher class for any Section Helper class that can route methods using a model name as argument. 
 */
class ThirdPartyAuditsHelper extends AppHelper {
	public $helpers = ['Html'];
	
	/**
	 * Call section's helper methods throught this dispatcher using a Model name.
	 * Can be used in functionalities that can manage all sections in one place under one view.
	 * 
	 * @param  string $name Method to call.
	 * @param  array  $args Arguments, first argument should always be a model name.
	 */
	public function beforeRender($viewFile) {
		$this->Html->addCrumb(__('Third Party Audits'), array('controller' => 'thirdPartyAudits', 'action' => 'index', 'admin' => false, 'plugin' => 'thirdPartyAudits'));
	}


}