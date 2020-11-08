<?php
Router::connect('/cron/index', array(
	'plugin' => 'Cron',
	'controller' => 'cron',
	'action' => 'index'
));

$cronJobs = ['hourly', 'daily', 'yearly'];
foreach ($cronJobs as $type) {
	Router::connect('/cron/' . $type . '/*', array(
		'plugin' => 'Cron',
		'controller' => 'cron',
		'action' => 'job',
		$type
	));
}