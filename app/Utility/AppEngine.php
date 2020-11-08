<?php
/**
 * TBD - placeholder for functionality that relates/affects to everything.
 *
 * @package app.Utility
 * 
 */
class AppEngine {

	protected static $defaultDbConfig = [
		'cake3' => [
			'database' => false,
			'username' => false,
			'password' => false,
			'host' => false,
			'port' => false,
			'unix_socket' => false,
			'prefix' => '',
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Mysql',
			'persistent' => false,
			'encoding' => 'utf8',
			'timezone' => 'UTC',
			'flags' => [],
			'cacheMetadata' => true,
			'log' => false,
			'quoteIdentifiers' => false
		],
		'phinx' => [
			'adapter' => 'mysql',
			'name' => false,
			'user' => false,
			'pass' => false,
			'host' => false,
			'port' => false,
			'unix_socket' => false,
			'table_prefix' => '',
			'charset' => 'utf8'
		]
	];

	/**
	 * Convesion between configuration formats - From => To.
	 * Example 'From' keys:
	 * 
	 *	'host' => '',
	 *	'port' => '',
	 *	'login' => '',
	 *	'password' => '',
	 *	'database' => '',
	 *	'prefix' => '',
	 *	'unix_socket' => '',
	 *
	 * @var array
	 */
	protected static $convertConfigKeys = [
		'cake3' => [
			'login' => 'username'
		],
		'phinx' => [
			'database' => 'name',
			'login' => 'user',
			'password' => 'pass',
			'prefix' => 'table_prefix'
		]
	];

	/**
	 * Method first validates ConnectionManager accessibility and then tries to retrieve running PDO instance.
	 * 
	 * @return mixed If success returns array ['name' => 'db_name', 'connection' => PDO], otherwise false on failure.
	 */
	public static function getPdoInstance($configName = null) {
		if (class_exists('ConnectionManager') && method_exists('ConnectionManager', 'getDataSource')) {
			if ($configName === null) {
				$dbConfig = self::_getDbConfigProperties();
				$keys = array_keys($dbConfig);
				
				$pdoInstances = [];
				foreach ($keys as $configName) {
					$PdoInstanceArr = self::_getPdoConnection($configName);
					$db = ConnectionManager::$config->{$configName};

					$pdoInstances[$configName] = $PdoInstanceArr;
				}

				return $pdoInstances;
			}

			return self::_getPdoConnection($configName);
		}

		return false;
	}

	// get the database instance connection via connection manager.
	protected static function _getPdoConnection($configName) {
		$pdo = ConnectionManager::getDataSource($configName)->getConnection();
		$db = ConnectionManager::$config->{$configName};

		return [
			'connection' => $pdo,
			'name' => $db['database']
		];
	}

	/**
	 * Directly read current database configuration. For easier/external accessibility purposes.
	 */
	public static function readDbConfig($configName = null, $convertTo = null) {
		$dbConfig = self::_getDbConfigProperties();

		$configs = [];
		foreach ($dbConfig as $key => $var) {
			$configs[$key] = self::normalizeDbConfig($var);
		}

		$configs = self::_convertConfigs($configs, $convertTo);

		if ($configName === null) {
			return $configs;
		}

		if (!isset($configs[$configName])) {
			return false;
		}

		return $configs[$configName];
	}

	// get the database.php class instance properties
	protected static function _getDbConfigProperties() {
		if (!class_exists('DATABASE_CONFIG')) {
			$file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'database.php';

			if (!file_exists($file)) {
				throw new \Exception('Cannot find database.php');
			}

			include_once $file;
		}

		$instance = new DATABASE_CONFIG();
		return get_object_vars($instance);
	}

	// convert config arrays from cake2 format to other formats
	protected static function _convertConfigs($configs, $vendor) {
		foreach ($configs as $name => $cfg) {
			foreach (self::$convertConfigKeys[$vendor] as $convertFrom => $convertTo) {
				if (isset($cfg[$convertFrom])) {
					$configs[$name][$convertTo] = $cfg[$convertFrom];
					unset($configs[$name][$convertFrom]);
				}
			}

			$configs[$name] = array_merge(self::$defaultDbConfig[$vendor], $configs[$name]);
		}

		return $configs;
	}

	// noramalize the database config just in case there is something missing or something un-needed
	public static function normalizeDbConfig($cfg = []) {
		$cfg = array_merge([
			'host' => '',
			'login' => '',
			'password' => '',
			'database' => '',
			'port' => '',
			'prefix' => '',
			'unix_socket' => ''
		], $cfg);

		return [
			'host' => $cfg['host'],
			'login' => $cfg['login'],
			'password' => $cfg['password'],
			'database' => $cfg['database'],
			'port' => $cfg['port'],
			'prefix' => $cfg['prefix'],
			'unix_socket' => $cfg['unix_socket'],
		];
	}

}
