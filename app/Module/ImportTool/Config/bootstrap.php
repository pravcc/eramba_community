<?php
App::uses('ImportToolModule', 'ImportTool.Lib');

$cacheOptions = Configure::read('cacheOptions');
Cache::config('ImportTool', am(
	array(
		'duration'=> '+1 day',
		'prefix' => 'cake_import_tool_',
		'groups' => array('ImportTool')
	), 
	$cacheOptions
));