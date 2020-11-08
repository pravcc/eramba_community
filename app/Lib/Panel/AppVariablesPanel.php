<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('VariablesPanel', 'DebugKit.Lib/Panel');

/**
 * Provides debug information on the View variables.
 */
class AppVariablesPanel extends VariablesPanel {

	public $title = 'Variables';

	public $elementName = 'variables_panel';
/**
 * beforeRender callback
 *
 * @param Controller $controller Controller object.
 * @return array
 */
	public function beforeRender(Controller $controller) {
		$viewVars = parent::beforeRender($controller);

		array_walk_recursive($viewVars, 'self::modify');
		return $viewVars;
	}

	// convert the large outputs that may have many objects inside, to more compatible output
	public static function modify(&$item, $key) {
	    if (is_object($item)) {
	    	$item = new \ReflectionObject($item);
	    }
	}
}
