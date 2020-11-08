<?php
App::uses('ModuleBase', 'Lib');
class CronModule extends ModuleBase {
	const ACTION_NAME = 'job';

	public static $jobs = [
		'hourly' => [
			'EmailQueueCron' => 'EmailQueueCron',
			'LdapSyncCron' => 'LdapSync.LdapSyncCron',
			'DashboardCron' => 'Dashboard.DashboardCron',
			'DashboardCalendarCron' => 'Dashboard.DashboardCalendarCron',
			'News' => 'News.NewsCron',
		],
		'daily' => [
			'BackupRestoreCron' => 'BackupRestore.BackupRestoreCron',
			'ObjectStatusCron' => 'ObjectStatus.ObjectStatusCron',
			'AdvancedFiltersCron' => 'AdvancedFilters.AdvancedFiltersCron',
			// 'AwarenessCron' => 'AwarenessCron',
			'WidgetCron' => 'Widget.WidgetCron',
			'AutoUpdateCron' => 'AutoUpdateCron',
			'DashboardCron' => 'Dashboard.DashboardCron'
		],
		'yearly' => [
			'AuditsCron' => 'AuditsCron'
		]
	];

	/**
	 * Add a custom task to the CRON registry.
	 */
	public static function addTask($type, $name, $className)
	{
		self::$jobs[$type][$name] = $className;
	}

	public static function taskRequest(Controller $Controller, $requestId, $task)
	{
		$url = Router::url([
			'plugin' => 'cron',
			'controller' => 'cron',
			'action' => 'task',
			$requestId,
			$task
		], true);

		CakeLog::write('Cron', 'Cron is redirecting to the next task:' . PHP_EOL . $url);
		header("Location: " . $url);
		exit;
	}
}
