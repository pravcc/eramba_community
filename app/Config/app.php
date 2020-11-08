<?php
/**
 * Override any configuration for the app locally, open and modify file app_local.php
 * 
 * Example for $config:
 * 
$config = [
	// Config values belongs to cake's core
	'Config' => [
		'language' => 'spa'
	],

	// Eramba values have everything for the app
	'Eramba' => [
		// App version
		'version' => 'e1.0.6.999',

		// Offline version of eramba
		'offline' => false,

		// Settings as they are in database `settings` table, $keys are identical
		'Settings' => [
			'DB_SCHEMA_VERSION' => '',
			'CLIENT_ID' => '',
			'BRUTEFORCE_WRONG_LOGINS' => '',
			'BRUTEFORCE_SECONDS_AGO' => '',
			'DEFAULT_CURRENCY' => '',
			'SMTP_USE' => '',
			'SMTP_HOST' => '',
			'SMTP_USER' => '',
			'SMTP_PWD' => '',
			'SMTP_TIMEOUT' => '',
			'SMTP_PORT' => '',
			'NO_REPLY_EMAIL' => '',
			'CRON_SECURITY_KEY' => '',
			'BRUTEFORCE_BAN_FOR_MINUTES' => '',
			'BANNERS_OFF' => '',
			'DEBUG' => '2',

			// Email debug enabled will only log emails that were supposed to be send
			'EMAIL_DEBUG' => '1',

			// Email debug in addition can be configured as string value for custom email address as recepient
			'EMAIL_DEBUG' => 'your@email.org',

			'RISK_APPETITE' => '',
			'USE_SSL' => '',
			'TIMEZONE' => '',
			'BACKUPS_ENABLED' => '',
			'BACKUP_DAY_PERIOD' => '',
			'BACKUP_FILES_LIMIT' => '',
			'EMAIL_NAME' => '',
		],

		// Proxy configuration for connections
		'Proxy' => [
			'USE_PROXY' => '0',
			'PROXY_HOST' => '',
			'PROXY_PORT' => '',
			'USE_PROXY_AUTH' => '0',
			'PROXY_AUTH_USER' => '',
			'PROXY_AUTH_PASS' => ''
		],

		// Customize API endpoint for support requests locally
		'SUPPORT_API_URL' => 'https://support.eramba.org',

		// For devs and testing its possible to disable security features within the entire app
		'DISABLE_SECURITY' => false

		// For devs to completely hide the annoying "debug enabled" warning notification
		'DISABLE_DEBUG_NOTIFICATION' => false,

		// Preview section for the app's testing purposes
		'ENABLE_PREVIEW_SECTION' => false,

		// Set to true to make update process fail during updating to the next version
		'TRIGGER_UPDATE_FAIL' => false,

		'Preview' => [
			'PATH' => APP . 'Test' . DS . 'app_preview' . DS
		],

		// True to allow running any type of CRON job without a restrictions.
		// For debugging only.
		'CRON_DISABLE_VALIDATION' => false,

		// Enable database query general logs and slow logs
		// Brew MariaDB default path to log files is '/usr/local/var/mysql'
		'ENABLE_SQL_LOGS' => false 
	]
];
 */


/**
 * App primary configuration file.
 * 
 * @package       app.Config
 */


/**
 * 1. ERAMBA CONFIGURATION.
 * 
 */

/**
 * App version.
 */
$versionFile = file(ROOT . DS . 'VERSION');
$version = trim(array_pop($versionFile));

/**
 * Database settings stored in configure to be able to modify them during runtime if needed.
 */

// If we are using CLI console while having a blank database, lets not throw missing table exception
// by trying to read configuration settings for eramba from database
$settings = [];

// read settings from cache to speed up this logic
if (($settings = Cache::read('settings_list', 'settings')) === false) {
	$ds = ConnectionManager::getDataSource('default');
	$ds->cacheSources = false;
	
	if (in_array('settings', $ds->listSources())) {
		App::uses('Model', 'Model');
		$modelConfig = ['table' => 'settings', 'name' => 'BootstrapSetting', 'ds' => 'default'];
		$settings = (new Model($modelConfig))->find('list', [
		    'fields' => ['BootstrapSetting.variable', 'BootstrapSetting.value'],
		    'recursive' => -1
		]);

		// write settings to cache
		Cache::write('settings_list', $settings, 'settings');
	}
}



/**
 * Proxy settings.
 */
require_once 'custom_settings.php';

$config['Eramba'] = [
	'version' => $version,
	'offline' => false,
	'Settings' => $settings,
	'Proxy' => [
		'USE_PROXY' => USE_PROXY,
		'PROXY_HOST' => PROXY_HOST,
		'PROXY_PORT' => PROXY_PORT,
		'USE_PROXY_AUTH' => USE_PROXY_AUTH,
		'PROXY_AUTH_USER' => PROXY_AUTH_USER,
		'PROXY_AUTH_PASS' => PROXY_AUTH_PASS
	],

	// API URL
	'SUPPORT_API_URL' => 'https://support.eramba.org',
	'DISABLE_SECURITY' => false,
	'DISABLE_DEBUG_NOTIFICATION' => false,
	'ENABLE_PREVIEW_SECTION' => false,
	'Preview' => [
		'PATH' => APP . 'Test' . DS . 'app_preview' . DS
	]
];

/**
 * 2. OTHER CONFIGURATION.
 * 
 */


// ACL config
Configure::write('CacheDbAclConfig','acl');
Configure::write('CacheDbAclAro','User.Group');

// CakePdf
Configure::write('CakePdf', array(
	'engine' => 'CakePdf.Mpdf',
	'orientation' => 'landscape',
));

/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter . By Default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 *		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 * 		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
	'CacheDispatcher'
));

/**
 * Default language for the application.
 */
Configure::write('Config.language', 'eng');


/**
 * Return Eramba configuration.
 */
return $config;