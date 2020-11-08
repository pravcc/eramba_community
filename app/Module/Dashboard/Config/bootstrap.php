<?php
App::uses('DashboardModule', 'Dashboard.Lib');

$cacheOptions = Configure::read('cacheOptions');
Cache::config('Dashboard', am(
	array(
		'duration'=> '+1 day',
		'prefix' => 'eramba_dashboard_',
		'groups' => array('Dashboard')
	), 
	$cacheOptions
));