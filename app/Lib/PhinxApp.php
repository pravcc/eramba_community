<?php
/**
 * Phinx helper class to manage database migrations during runtime.
 * 
 * @package       app.Utility
 */

/**
 * Manage Phinx migrations independently or also externally.
 */
class PhinxApp {

	protected static $_loaded = false;
	protected static $_phinxApp = null;

	/**
	 * Phinx\Wrapper\TextWrapper instance.
	 * 
	 * @var null|TextWrapper
	 */
	protected $_phinxWrap = null;

	public function __construct($config = 'default') {
		if (self::$_loaded === false) {
			self::$_phinxApp = self::_autoload();
			self::$_loaded = true;
		}

		$this->_initPhinx($config);
	}

	/**
	 * Path to the upgrade folder. 
	 * 
	 * @return string
	 */
	public static function upgradePath() {
		return dirname(__DIR__) . '/upgrade';
	}

	/**
	 * Path to the phinx standalone package in ROOT/app/Vendor/phinx.
	 * 
	 * @return string
	 */
	public static function phinxPath() {
		return self::upgradePath() . '/vendor/robmorgan/phinx';
	}

	/**
	 * Autoload standalone phinx package.
	 */
	protected static function _autoload() {
		$PhinxApp = require_once self::phinxPath() . '/app/phinx.php';

		// Older PHP has a problem retrieving a class instance using require_once, this handles it
		if (!$PhinxApp instanceof Phinx\Console\PhinxApplication) {
			$PhinxApp = new Phinx\Console\PhinxApplication();
		}

		return $PhinxApp;
	}

	/**
	 * Initialize Phinx instance to manage datasources.
	 */
	protected function _initPhinx($configName = 'default') {
		$this->_phinxWrap = new Phinx\Wrapper\TextWrapper(self::$_phinxApp, [
			'configuration' => self::upgradePath() . '/phinx.php',
			'parser' => 'php',
			'environment' => $configName,
			'format' => 'json'
		]);

		return $this->_phinxWrap;
	}

	/**
	 * Status of the current migrations.
	 * 
	 * @return boolean True if all available migrations are already processed, False otherwise.
	 */
	public function getStatus() {
		$output = $this->_phinxWrap->getStatus();
		$exitCode = $this->_phinxWrap->getExitCode();
		
		// return true if success
		return ($exitCode === 0);
	}

	/**
	 * Process all available migrations pending to be migrated on the current database.
	 *
	 * @param null|string $target Target to which migrate the database, Null by default which migrates entire database.
	 * @return boolean            True on success, False otherwise.
	 */
	public function getMigrate($target = null) {
		$output = $this->_phinxWrap->getMigrate(null, $target);
		$exitCode = $this->_phinxWrap->getExitCode();

		// return true if success
		return ($exitCode === 0);
	}

}