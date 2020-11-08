<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/*
 * Initialized in Cake's bootstrap.php
 * 
 * App::uses('ErrorHandler', 'Error');
 * App::uses('Configure', 'Core');
 * App::uses('CakePlugin', 'Core');
 * App::uses('Cache', 'Cache');
 * App::uses('CakeObject', 'Core');
 * App::uses('Object', 'Core');
 * App::uses('Multibyte', 'I18n'); 
 */

App::uses('SystemHealthLib', 'Lib');
App::uses('CakeTime', 'Utility');
App::uses('CakeNumber', 'Utility');
App::uses('StatusesLib', 'Lib');
App::uses('CakeLog', 'Log');
App::uses('AppModule', 'Lib');
App::uses('CacheDbAcl', 'Lib');
App::uses('ErambaHttpSocket', 'Network/Http');
App::uses('ConnectionManager', 'Model');

if (!SystemHealthLib::phpVersion()) {
	echo __('PHP version %s is required for the application at least. Your PHP version is %s. Please update your system with the latest version of PHP and related extensions, if possible.', SystemHealthLib::PHP_VERSION_REQUIRED, PHP_VERSION);
	exit;
}

$isCli = PHP_SAPI === 'cli';

// this needs to be first because of cache config for settings
require_once 'cacheConfig.php';

/*
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */
try {
	App::uses('PhpReader', 'Configure');

    Configure::config('default', new PhpReader());
    Configure::load('app', 'default', false);
} catch (\Exception $e) {
    exit($e->getMessage() . "\n");
}

require_once 'shared_constants.php';
require_once 'bootstrap_functions.php';// we require php functions with app-wide general use.

/*
 * Load an environment local configuration file.
 * You can use a file like app_local.php to provide local overrides to your
 * shared configuration.
 */
if (file_exists(__DIR__ . DS . 'app_local.php')) {
	Configure::load('app_local', 'default');
}

// Security Configuration
Configure::write('Security.salt', Configure::consume('Eramba.Settings.SECURITY_SALT'));

// compatibility constants for removed settings component
$settings = Configure::read('Eramba.Settings');
foreach ($settings as $key => $value) {
	if (!defined($key)) {
		define($key, $value);
	}
}

// Debug based on DB setting by default, can be overriden in the app_local file for example.
Configure::write('debug', Configure::read('Eramba.Settings.DEBUG') ? 2 : 0);
if (Configure::read('debug')) {
	// set the debug also for database queries (debug is still 0 when DS is first used to get Settings in app.php)
	// @see DboSource::__construct()
	ConnectionManager::getDataSource('default')->fullDebug = true;
}

// logging for requests using dispatch filters while debug is enabled
$dispatcherCount = count(Configure::read('Dispatcher.filters'));

// append our own dispatcher filter for debugging all requests
Configure::write('Dispatcher.filters.' . $dispatcherCount, 'RequestDispatcher');

// Configure mysql variable to toggle sql query logging
if (Configure::read('Eramba.ENABLE_SQL_LOGS')) {
	$connection = ConnectionManager::getDataSource('default');

	$sql = $connection->execute("SET GLOBAL general_log = 'ON'");
	$sql &= $connection->execute("SET GLOBAL slow_query_log = 'ON'");
	$sql &= $connection->execute("FLUSH STATUS");

	if (!$sql) {
		trigger_error('SQL Logging could not be enabled.');
	}
}


// dd(Configure::read());
/*
 * Include the CLI bootstrap overrides.
 */
// if ($isCli) {
//     require __DIR__ . '/bootstrap_cli.php';
// }

/**
 * Datetime configuration.
 */
$timezone = Configure::read('Eramba.Settings.TIMEZONE');
if ($timezone != null) {
	date_default_timezone_set($timezone);
}

/**
 * Proxy configuration.
 */
ErambaHttpSocket::setProxyConfig(Configure::consume('Eramba.Proxy'));


/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models/', '/next/path/to/models/'),
 *     'Model/Behavior'            => array('/path/to/behaviors/', '/next/path/to/behaviors/'),
 *     'Model/Datasource'          => array('/path/to/datasources/', '/next/path/to/datasources/'),
 *     'Model/Datasource/Database' => array('/path/to/databases/', '/next/path/to/database/'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions/', '/next/path/to/sessions/'),
 *     'Controller'                => array('/path/to/controllers/', '/next/path/to/controllers/'),
 *     'Controller/Component'      => array('/path/to/components/', '/next/path/to/components/'),
 *     'Controller/Component/Auth' => array('/path/to/auths/', '/next/path/to/auths/'),
 *     'Controller/Component/Acl'  => array('/path/to/acls/', '/next/path/to/acls/'),
 *     'View'                      => array('/path/to/views/', '/next/path/to/views/'),
 *     'View/Helper'               => array('/path/to/helpers/', '/next/path/to/helpers/'),
 *     'Console'                   => array('/path/to/consoles/', '/next/path/to/consoles/'),
 *     'Console/Command'           => array('/path/to/commands/', '/next/path/to/commands/'),
 *     'Console/Command/Task'      => array('/path/to/tasks/', '/next/path/to/tasks/'),
 *     'Lib'                       => array('/path/to/libs/', '/next/path/to/libs/'),
 *     'Locale'                    => array('/path/to/locales/', '/next/path/to/locales/'),
 *     'Vendor'                    => array('/path/to/vendors/', '/next/path/to/vendors/'),
 *     'Plugin'                    => array('/path/to/plugins/', '/next/path/to/plugins/'),
 * ));
 *
 */


// separate custom made parts same as plugins into its own folder
App::build(array(
    'Plugin' => array(AppModule::rootPath())
), App::APPEND);

App::build(['Model/Trait' => [APP . 'Model' . DS . 'Trait' . DS]], App::REGISTER);
App::build(['Controller/Crud/Trait' => [APP . 'Controller' . DS . 'Crud' . DS . 'Trait' . DS]], App::REGISTER);
App::build(['Controller/Trait' => [APP . 'Controller' . DS . 'Trait' . DS]], App::REGISTER);
App::build(['View/Renderer/Processor/Trait' => [APP . 'View' . DS . 'Renderer' . DS . 'Processor' . DS . 'Trait' . DS]], App::REGISTER);

App::build([
	'Model/Interface' => [APP . 'Model' . DS . 'Interface' . DS],
	'Controller/Interface' => [APP . 'Controller' . DS . 'Interface' . DS],
	'View/Helper/Interface' => [APP . 'View' . DS . 'Helper' . DS . 'Interface' . DS]
], App::REGISTER);

// App::build([
// 	'Model/FieldData' => ['%s' . 'Model' . DS . 'FieldData' . DS],
// 	'Model/FieldData/Extensions' => ['%s' . 'Model' . DS . 'FieldData' . DS . 'Extensions' . DS]
// ], App::REGISTER);


/**
 * Custom Inflector rules, can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */

CakePlugin::load('Acl', array('bootstrap' => true));
CakePlugin::load('AclExtras');

CakePlugin::load('Uploader');
CakePlugin::load('CsvView');
CakePlugin::load('DebugKit');
CakePlugin::load('Search');

CakePlugin::load('HtmlPurifier', array('bootstrap' => true));

CakePlugin::load('CakePdf', array('bootstrap' => true, 'routes' => true));

CakePlugin::load('Migrations');
CakePlugin::load('AuditLog');
CakePlugin::load('Utils');
CakePlugin::load('Crud');

Purifier::config('Strict', array(
	'HTML.AllowedElements' => '',
	'HTML.AllowedAttributes' => '',
	'HTML.TidyLevel' => 'heavy',
	'Cache.SerializerPath' => APP . 'tmp' . DS . 'cache' . DS . 'purifier'
));

Purifier::config('Editor', array(
	'HTML.AllowedElements' => 'a, em, blockquote, p, strong, pre, code, span,ul,ol,li,img,h1,h2,h3,h4,h5,h6,br,table,tbody,thead,tr,th,td, div,sub,sup',
	'HTML.AllowedAttributes' => 'a.href, a.title, img.src, img.alt, table.style, span.style, p.style',
	'HTML.TidyLevel' => 'heavy',
	'Cache.SerializerPath' => APP . 'tmp' . DS . 'cache' . DS . 'purifier'
));

/**
 * Configures default file logging options
 */

CakeLog::config('debug', array(
	'engine' => 'File',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'File',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));
CakeLog::config('eramba', array(
	'engine' => 'File',
	// 'types' => array('notice', 'warning', 'error'),
	'scopes' => array('eramba'),
	'file' => 'eramba.log',
));
CakeLog::config('email', array(
	'engine' => 'File',
	'scopes' => array('email'),
    'file' => 'email'
));
CakeLog::config('systemLog', array(
	'engine' => 'File',
	'scopes' => array('systemLog'),
	'file' => 'systemLog.log',
));
CakeLog::config('updates', array(
	'engine' => 'File',
	'scopes' => array('updates'),
	'file' => 'updates.log',
));
CakeLog::config('NotificationSystem', array(
	'engine' => 'File',
	'scopes' => array('NotificationSystem'),
	'file' => 'NotificationSystem.log',
));
CakeLog::config('SystemHealth', array(
	'engine' => 'File',
	'scopes' => array('SystemHealth'),
	'file' => 'SystemHealth.log',
));

/**
 * Configures currencies for the app
 */
CakeNumber::addFormat('EUR', array(
	'wholeSymbol' => ' &#8364;',
	'wholePosition' => 'after',
	'fractionSymbol' => false,
	'fractionPosition' => 'after',
	'zero' => 0,
	'places' => 2,
	'thousands' => ' ',
	'decimals' => ',',
	'negative' => '-',
	'escape' => false
));
// @see CakeNumber::$_currencies;
$cakeDefinedCurrencies = array('AUD', 'CAD', 'USD', 'EUR', 'GBP', 'JPY');

$currencySetting = Configure::read('Eramba.Settings.DEFAULT_CURRENCY');
// we add a custom currency format if its not already defined in cake
if (!in_array($currencySetting, $cakeDefinedCurrencies)) {
	$currencies = getCustomCurrencies();
	if (!isset($currencies[$currencySetting])) {
		trigger_error(__('Currency configuration is invalid or not available.'));
	}

	CakeNumber::addFormat($currencySetting, array(
		'wholeSymbol' => ' ' . $currencySetting,
		'wholePosition' => 'after',
		'zero' => 0
	));
}

// default currecy for the app
CakeNumber::defaultCurrency($currencySetting);

// Cron loaded with priority
AppModule::load('Cron');

// load all modules (app plugins)
AppModule::loadAll();

// we include and enable preview sections for testing purposes

if (Configure::read('Eramba.ENABLE_PREVIEW_SECTION')) {
	// Configure::write('Eramba.Preview.path', APP . 'Test' . DS . 'app_preview' . DS);
	$path = Configure::read('Eramba.Preview.PATH');

	include $path . 'Config' . DS . 'bootstrap.php';
	include $path . 'Config' . DS . 'routes.php';
}

//inflector rule for compliance analysis
Inflector::rules('singular', array(
    'uninflected' => array('compliance analysis', 'business impact analysis')
));