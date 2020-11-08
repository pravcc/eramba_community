<?php
App::uses('CrudAction', 'Crud.Controller/Crud');
App::uses('Cron', 'Model');
App::uses('CakeText', 'Utility');
App::uses('CronException', 'Cron.Error');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');

/**
 * Handles 'CRON' Crud actions
 */
class CronCrudAction extends CrudAction {
	use CrudActionTrait;

	protected $_settings = array(
		'enabled' => true,
		'view' => 'job'
	);

	/**
	 * Constant representing the scope of this action
	 *
	 * @var integer
	 */
	const ACTION_SCOPE = CrudAction::SCOPE_MODEL;

	/**
	 * Errors for the current CRON Job action.
	 * 
	 * @var null|array
	 */
	protected $_errors = null;

	/**
	 * Wheter this action went successfully or with issues.
	 * 
	 * @var null|boolean
	 */
	protected $_success = null;

	/**
	 * Initiate CRON request by storing a generated uuid and hold it until this CRON completes,
	 * to provide easily accessible groupping for records anywhere which might depend on it.
	 * 
	 * @return strin UUID.
	 */
	protected function _initRequestId() {
		// Class 'String' was deprecated in CakePHP 2.7 and replaced by 'CakeText' (Issue #41)
		$UuidClass = class_exists('CakeText') ? 'CakeText' : 'String';
		Cron::setRequestId($UuidClass::uuid());

		return Cron::requestId();
	}

	/**
	 * Executes and handles each type of Cron job.
	 * 
	 * @return bool True on success, False otherwise. Return value depends on event->stopped flag.
	 */
	protected function _handle($type, $key) {
		ignore_user_abort(true);
		set_time_limit(600); //10 min
			
		// reset class variables and prepare cron execution
		$this->_reset();

		try {
			$this->_executeJob($type, $key);
		} catch (CronException $e) {
			$this->setError($e->getFullMessage());
		}

		// and render results
		$this->_trigger('beforeRender', [
			'success' => $this->isSuccess(),
			'type' => $type,
			'errors' => $this->getError()
		]);
	}

	/**
	 * Internal method to execute the cron job.
	 * 
	 * @return void
	 */
	protected function _executeJob($type, $key) {
		$success = true;
		$controller = $this->_controller();
		$model = $this->_model();

		unset($controller->request->data);

		// startup unique request ID for the entire CRON process.
		$requestId = $this->_initRequestId();

		try {
			// trigger beforeJob callback for this cron
			$subject = $this->_trigger('beforeJob', compact('type', 'key', 'requestId'));
			if ($subject->stopped) {
				throw new CronException(__('CRON job has been terminated right before the main process could begin, please try again or try contacting our support team'));
			}

			// trigger the main process of a cron job type
			// $subject = $this->_trigger($type, compact('requestId'));
			if ($subject->stopped) {
				throw new CronException(__('CRON job has been terminated during the main process, everything should be reverted to it\'s original state, please try again or try contacting our support team'));
			}
		} catch (CakeException $e) {
			$errorMessage = $e->getMessage();
			$this->_triggerAfter($type, false, $errorMessage);

			throw $e;
		}

		$this->_triggerAfter($type, true, __('Your CRON Job is running in the background. Status can be viewed on the CRON index.'));
	}

	protected function _triggerAfter($type, $success, $message = null) {
		// optionally cleanup is managed for each listener in afterJob
		$subject = $this->_trigger('afterJob', [
			'type' => $type,
			'success' => $success,
			'message' => $message
		]);
	}

	/**
	 * Append error message to the stack of error messages and set status to not successful for this CRON Job.
	 * 
	 * @param string $message Error message.
	 */
	public function setError($message) {
		$this->_errors[] = $message;
		$this->_success = false;
	}

	/**
	 * Get the error messages as array.
	 * 
	 * @return array
	 */
	public function getError() {
		return $this->_errors;
	}

	/**
	 * Check if current CRON job went successfully.
	 * 
	 * @return boolean
	 */
	public function isSuccess() {
		return $this->_success;
	}

	/**
	 * Reset this action to default state.
	 * 
	 * @return void
	 */
	protected function _reset() {
		$this->_errors = [];
		$this->_success = true;
	}

}