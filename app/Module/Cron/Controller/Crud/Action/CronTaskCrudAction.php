<?php
App::uses('CrudAction', 'Crud.Controller/Crud');
App::uses('Cron', 'Model');
App::uses('CakeText', 'Utility');
App::uses('CronException', 'Cron.Error');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');
App::uses('CakeLog', 'Log');
App::uses('Debugger', 'Utility');
App::uses('AppErrorHandler', 'Error');

/**
 * Handles 'CRON' Crud actions
 */
class CronTaskCrudAction extends CrudAction {
	use CrudActionTrait;

	protected $_settings = array(
		'enabled' => true,
		'view' => 'task'
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
	 * Executes and handles each type of Cron job.
	 * 
	 * @return bool True on success, False otherwise. Return value depends on event->stopped flag.
	 */
	protected function _handle($requestId, $task = null) {
		$model = $this->_model();

		ignore_user_abort(true);
		set_time_limit(600); //10 min

		// reset class variables and prepare cron execution
		$this->_reset();

		try {
			$this->_executeJob($requestId, $task);
		} catch (CronException $e) {
			$this->setError($e->getFullMessage());
		}

		$cron = $model->find('first', [
			'conditions' => [
				'Cron.request_id' => $requestId
			],
			'recursive' => -1
		]);

		$cronTasks = $model->CronTask->find('all', [
			'conditions' => [
				'CronTask.cron_id' => $cron['Cron']['id'],
			],
			'recursive' => -1
		]);

		$vars = [
			'cron' => $cron,
			'cronTasks' => $cronTasks,
			'success' => $this->isSuccess(),
			'task' => $task,
			'errors' => $this->getError()
		];

		// and render results
		$this->_trigger('beforeRender', $vars);

		$this->_controller()->set($vars);
	}

	/**
	 * Internal method to execute the cron job.
	 * 
	 * @return void
	 */
	protected function _executeJob($requestId, $task = null) {
		$success = true;
		$controller = $this->_controller();
		$model = $this->_model();

		unset($controller->request->data);
		
		$cron = $model->find('first', [
			'conditions' => [
				'Cron.request_id' => $requestId,
				'Cron.status' => Cron::STATUS_PENDING
			],
			'recursive' => -1
		]);

		if (empty($cron)) {
			throw new CronException(sprintf('Cron with request ID "%s" is not accepting any more task requests.', $requestId));
		}

		$type = $cron['Cron']['type'];

		$taskData = $model->CronTask->find('first', [
			'conditions' => [
				'CronTask.task' => $task,
				'CronTask.cron_id' => $cron['Cron']['id']
			],
			'recursive' => -1
		]);

		// lets check statuses of the required task
		if (!empty($taskData)) {
			if ($taskData['CronTask']['status'] == CronTask::STATUS_PENDING) {
				throw new CronException(sprintf('Task "%s" is already running, please wait.', $task) , 1);
			}

			if ($taskData['CronTask']['status'] == CronTask::STATUS_SUCCESS) {
				throw new CronException(sprintf('Task "%s" has been already processed with Success status.', $task) , 1);
			}

			if ($taskData['CronTask']['status'] == CronTask::STATUS_ERROR) {
				throw new CronException(sprintf('Task "%s" has been already processed with Error status.', $task) , 1);
			}
		}
		// otherwise create pending record of the task and run the task 
		else {
			$model->CronTask->create([
				'cron_id' => $cron['Cron']['id'],
				'task' => $task,
				'status' => CronTask::STATUS_PENDING
			]);

			$taskData = $model->CronTask->save();
		}
		
		$cronId = $taskData['CronTask']['cron_id'];
		try {
			$this->_trigger('beforeJob');

			// trigger the main process of a cron job type
			$subject = $this->_trigger($type, compact('requestId', 'cronId'));
			if ($subject->stopped) {
				throw new CronException(__('CRON task "%s" failed to process.', $task));
			}
		} catch (Exception $e) {
			// log the error
			AppErrorHandler::logException($e);

			$errorMessage = $e->getMessage();
			$msg = __('CRON task "%s" failed to process with error: %s.', $task, PHP_EOL.PHP_EOL.$errorMessage);
			$this->_triggerAfter($taskData, false, $msg);

			throw new CronException($msg);
		}

		$this->_triggerAfter($taskData, true, __('Your CRON Task %s has been completed successfully', $task));
	}

	protected function _triggerAfter($taskData, $success, $message = null) {
		if ($success) {
			$taskStatus = CronTask::STATUS_SUCCESS;
		}
		else {
			$taskStatus = CronTask::STATUS_ERROR;
		}

		$executionTime = scriptExecutionTime();

		$this->_model()->CronTask->set([
			'id' => $taskData['CronTask']['id'],
			'execution_time' => $executionTime,
			'completed' => CakeTime::format('Y-m-d H:i:s', CakeTime::fromString('now')),
			'status' => $taskStatus,
			'message' => $message
		]);
		$this->_model()->CronTask->save();

		$log = 'Cron task has been finished:' . PHP_EOL;		
		$log .= 'Task: ' . $taskData['CronTask']['task'] . PHP_EOL;
		$log .= 'Status: ' . CronTask::statuses($taskStatus) . PHP_EOL;
		$log .= 'Message: ' . $message . PHP_EOL;
		$log .= 'Execution Time: ' . $executionTime . PHP_EOL;

		CakeLog::write('Cron', $log);

		// this calls the next task request or manage final status on the cron record
		$this->_model()->handleTasks($this->_controller(), $taskData['CronTask']['cron_id']);

		$this->_trigger('afterJob');
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