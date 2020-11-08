<?php
App::uses('CakeObject', 'Core');

/**
 * Abstract base class for Update release classes.
 */
abstract class AbstractUpdate extends CakeObject {

	/**
	 * Error message in case one callback return false and failed the updating.
	 * 
	 * @return string Message.
	 */
	public function getMessage() {
		return null;
	}

	/**
	 * After update process completes updating. That is after files are copied, database migrations has run successfully
	 * and new version values take place.
	 * 
	 * @return bool True to continue, False to break updating.
	 */
	public function run() {
		return true;
	}

}