<?php
// lets use our helper class to load it up externally
$AppEngine = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Utility' . DIRECTORY_SEPARATOR . 'AppEngine.php';
include_once $AppEngine;

return [
	'Datasources' => AppEngine::readDbConfig(null, 'cake3')
];