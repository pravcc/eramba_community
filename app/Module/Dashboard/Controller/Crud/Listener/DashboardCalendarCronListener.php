<?php
App::uses('CronCrudListener', 'Cron.Controller/Crud');
App::uses('DashboardCalendar', 'Dashboard.Lib');

/**
 * Dashboard Calendar CRON.
 */
class DashboardCalendarCronListener extends CronCrudListener {

	public function implementedEvents() {
		return array(
			'Cron.beforeHandle' => 'beforeHandle',
			'Cron.hourly' => 'hourly'
		);
	}

	public function beforeHandle(CakeEvent $event) {
		$request = $this->_request();
		$model = $this->_model();
		$controller = $this->_controller();

		$this->DashboardCalendar = new DashboardCalendar();
	}

	/**
	 * Hourly CRON listener for Dashboards.
	 * Calculates once a day.
	 * 	
	 * @param  CakeEvent $event
	 * @return boolean True on success, False on failure.
	 */
	public function hourly(CakeEvent $event) {
		$ret = true;

		$ret = $this->DashboardCalendar->sync();
		if (!$ret) {
			throw new CronException(__('Dashboard Calendar failed to re-build events.'));
		}
	}

}
