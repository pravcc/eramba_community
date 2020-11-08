<?php
// lets use our helper class to load it up externally
$AppEngine = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Utility' . DIRECTORY_SEPARATOR . 'AppEngine.php';
include_once $AppEngine;

// first try to read the PDO instances running in the background
$environments = AppEngine::getPdoInstance(null);

// if that fails read the database configurations through the database file converted for Phinx already
if ($environments === false) {
    $environments = AppEngine::readDbConfig(null, 'phinx');
}

$environments['default_migration_table'] = 'phinxlog';
$environments['default_database'] = 'default';

return [
    'paths' => [
        'migrations' => __DIR__ . DIRECTORY_SEPARATOR . 'config/Migrations',
        'seeds' => __DIR__ . DIRECTORY_SEPARATOR . 'config/Seeds'
    ],
    'environments' => $environments
];