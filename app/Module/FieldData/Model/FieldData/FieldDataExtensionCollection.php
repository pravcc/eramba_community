<?php
/**
 * BehaviorCollection
 *
 * Provides management and interface for interacting with collections of behaviors.
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
 * @package       Cake.Model
 * @since         CakePHP(tm) v 1.2.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('ObjectCollection', 'Utility');
App::uses('CakeEventListener', 'Event');
App::uses('ClassRegistry', 'Utility');

/**
 * Model behavior collection class.
 *
 * Defines the Behavior interface, and contains common model interaction functionality.
 *
 * @package       Cake.Model
 */
class FieldDataExtensionCollection extends ObjectCollection implements CakeEventListener {

	// reference to parent class
	public $FieldData = null;
	/**
	 * Attaches an object and loads extensions.
	 */
	public function init($FieldData, array $extensions = array()) {
		$this->FieldData = $FieldData;

		if (!empty($extensions)) {
			foreach ((array) $extensions as $name => $config) {
				$this->load($name, $config);
			}
		}
	}

	public function load($object, $config = array()) {
		list($plugin, $class) = pluginSplit($object, true);
		if (isset($this->_loaded[$class])) {
			return $this->_loaded[$class];
		}
		$object = $class;
		$class .= 'Extension';
		App::uses($class, $plugin . 'Model/FieldData/Extension');

		if (!class_exists($class)) {
			throw new CakeException(sprintf('Class extension %s doesnt exist.', $class));
		}

		$this->_loaded[$object] = new $class($this, $config);
		$this->_loaded[$object]->setup(ClassRegistry::getObject($this->FieldData), $config);

		$enable = isset($settings['enabled']) ? $settings['enabled'] : true;
		if ($enable === true) {
			$this->enable($object);
		}

		return $this->_loaded[$object];
	}

/**
 * Returns the implemented events that will get routed to the trigger function
 * in order to dispatch them separately on each behavior
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'FieldData.initialize' => 'trigger',
			'FieldData.beforeFind' => 'trigger'
		);
	}

}
