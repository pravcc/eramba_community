<?php
/**
 * SessionComponent. Provides access to Sessions from the Controller layer
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Controller.Component
 * @since         CakePHP(tm) v 0.10.0.1232
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('SessionComponent', 'Controller/Component');
App::uses('CakeSession', 'Model/Datasource');

class AppSessionComponent extends SessionComponent {
	public $components = array('Flash');

	public function setFlash($message, $element = 'default', $params = array(), $key = 'flash') {
		$options = [
			'element' => $element,
			'params' => $params,
			'key' => $key
		];

		$this->Flash->set($message, $options);
	}

}