<?php
App::uses('CronModule', 'Cron.Lib');

CakeLog::config('Cron', array(
	'engine' => 'File',
	'scopes' => array('Cron'),
	'file' => 'Cron.log',
));