<?php
App::uses('CronCrudListener', 'Cron.Controller/Crud');
App::uses('Cron', 'Model');
App::uses('CronModule', 'Cron.Lib');

/**
 * Dashboard CRON listener that handles all tasks that needs to be processed during the CRON.
 */
class CronSetupListener extends CronCrudListener {

	/**
	 * Specifically prioritized callbacks for this listener, as it is the primary core configuration
	 * for CRON jobs and some callbacks should be executed as first before the other listeners,
	 * and some last just after the others because they require to summarize what all has already happened.
	 * 
	 * @see    inspired by DebugKitListener class
	 * @return void
	 */
	public function implementedEvents() {
		return array(
			'Cron.beforeJob' => array('callable' => 'beforeJob', 'priority' => 1),
			'Cron.afterJob' => array('callable' => 'afterJob', 'priority' => 1),
			'Cron.beforeRender' => array('callable' => 'beforeRender', 'priority' => 5000)
		);
	}

	/**
	 * Authenticates configured security key for CRON jobs.
	 *
	 * @param  string $key Provided key for authentication
	 * @return boolean    True if authentication passes, False otherwise
	 */
	protected function _authenticate(CakeEvent $event) {
		return Configure::read('Eramba.Settings.CRON_SECURITY_KEY') === $event->subject->key;
	}

	/**
	 * General validation for the CRON job.
	 */
	protected function _validate(CakeEvent $event) {
		$valid = true;

		$valid &= !$this->_model()->cronTaskExists($event->subject->type);
		$valid &= !$this->_model()->isCronTaskRunning($event->subject->type);

		return $valid;
	}

	/**
	 * Startup the CRON job by configuring it as running at the moment.
	 */
	public function beforeJob(CakeEvent $event) {
		if ($event->subject->type === null) {
			throw new CronException(__('Cron job must have a type of the job (hourly/daily/yearly) configured when executed!'));
		}

		if (!$this->_authenticate($event)) {
			throw new CronException(__('The Security key you provided on the cron URL does not match what is defined at System / Settings / Crontab Security Key'));
		}

		if (!$this->_validate($event)) {
			throw new CronException(__("Your request to execute a %s CRON job is invalid, either it has been processed already or it is still being processed", $event->subject->type));
		}
	}

	/**
	 * Prioritized callback that saves the record about current cron job's status.
	 */
	public function afterJob(CakeEvent $event) {
		$status = $event->subject->success ? Cron::STATUS_PENDING : Cron::STATUS_ERROR;
		$message = $event->subject->message;
		// lets save the record about status of the current cron
		// questionable right now is if this would be better placed here or in afterJob callback in CronSetupListener
		$cron = $this->_model()->saveCronTaskRecord($event->subject->type, $status, $message);

		if ($event->subject->success) {
			$log = 'Cron ' . $event->subject->type . ' is starting...';
			CakeLog::write('Cron', $log);
			$this->_model()->handleTasks($this->_controller(), $cron['Cron']['id']);
		}
	}

	public function beforeRender(CakeEvent $event) {
		// process only for job action
		if ($event->subject->crud->action() instanceof CronCrudAction) {
			$this->_controller()->set([
				'success' => $event->subject->success,
				'type' => $event->subject->type,
				'errors' => $event->subject->errors
			]);
		}
	}


}
