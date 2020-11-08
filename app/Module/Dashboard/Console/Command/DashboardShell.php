<?php
/**
 * Acl Dashboard Shell.
 */
App::uses('AppShell', 'Console/Command');
App::uses('Dashboard', 'Dashboard.Lib');
App::uses('DashboardCalendar', 'Dashboard.Lib');
App::uses('DashboardUserSync', 'Dashboard.Lib');
App::uses('ClassRegistry', 'Utility');

/**
 * Shell for Dashboard ACOs
 *
 * @package		Dashboard.Console.Command
 */
class DashboardShell extends AppShell {

/**
 * Contains arguments parsed from the command line.
 *
 * @var array
 * @access public
 */
	public $args;

/**
 * Dashboard instance
 */
	public $Dashboard;

/**
 * Constructor
 */
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
		$this->Dashboard = new Dashboard();
	}

/**
 * Start up And load Acl Component / Aco model
 *
 * @return void
 **/
	public function startup() {
		parent::startup();
		// $this->Dashboard->startup();
		$this->Dashboard->Shell = $this;
	}

	public function sync_calendar() {
		$DashboardCalendar = new DashboardCalendar();

		$ret = true;

		$this->out('Dashboard Calendar synchronization started...');

		$ret &= $DashboardCalendar->sync();

		if ($ret) {
			$this->out('Dashboard Calendar has been synced successfully.');
		}
		else {
			$this->error('Error occured while syncing Dashboard Calendar, please try again.');
		}

		return $ret;
	}

/**
 * Sync the ACO table
 *
 * @return void
 **/
	public function sync() {
		$ret = true;

		$this->out('Dashboard KPIs synchronization started...');

		$ret &= $this->Dashboard->sync(['reset' => false, 'structure' => true, 'values' => true]);

		if ($ret) {
			$this->out('Dashboard KPIs have been synced successfully.');
		}
		else {
			$this->error('Error occured while syncing Dashboard KPIs, please try again.');
		}

		return $ret;
	}

	public function sync_user_dashboard() {
		$ret = true;

		$this->out('Dashboard KPIs synchronization started...');

		$DashboardUserSync = new DashboardUserSync();

		$ret &= $DashboardUserSync->sync();

		if ($ret) {
			$this->out('Dashboard KPIs have been synced successfully.');
		}
		else {
			$this->error('Error occured while syncing Dashboard KPIs, please try again.');
		}

		return $ret;
	}

	public function getOptionParser() {
		return parent::getOptionParser()
			->description(__("Dashboard Shell"))
			->addSubcommand('sync', [
				'help' => __('Sync KPIs in the entire system, without recalculating values.')
			])
			->addSubcommand('sync_user_dashboard', [
				'help' => __('Sync User dashboards.')
			])
			->addSubcommand('sync_calendar', [
				'help' => __('Sync Dashboard Calendar.')
			]);
	}

}
