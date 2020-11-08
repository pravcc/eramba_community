<?php
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
App::uses('ErambaHttpComponent', 'Controller/Component');
App::uses('ComponentCollection', 'Controller');
App::uses('ErambaHttpSocket', 'Network/Http');

/**
 * Application's class to work with Composer library explicitly via PHP.
 */
class AppComposer {
	// application working dir where composer.phar should be placed and composer.json located
	protected $_workingDir = APP . 'upgrade';

	// phar file name
	protected $_phar = 'composer.phar';

	// shell instance
	public $Shell = null;

	/**
	 * This variable will hold possible error messages that happens during the execution of composer.
	 * 
	 * @var array
	 */
	protected $_errors = [];

	/**
	 * This variable will toggle to true in case something has been put into $this->_errors.
	 * 
	 * @var null|boolean
	 */
	protected $_errorOccured = null;

	// out for Shell
	public function out($msg) {
		if ($this->Shell instanceof Shell) {
			return $this->Shell->out($msg);
		}
	}

	// error for Shell
	public function err($msg) {
		if ($this->Shell instanceof Shell) {
			return $this->Shell->err($msg);
		}
	}

	/**
	 * Resets the class to a default state just before execution.
	 * 
	 * @return void
	 */
	protected function _reset() {
		$this->_errorOccured = null;
		$this->_errors = [];
	}

	/**
	 * Sets an error message during the execution if there was some issue.
	 * 
	 * @param string $message Error message.
	 */
	protected function _setError($message) {
		$this->_errorOccured = true;
		$this->_errors[] = $message;
	}

	/**
	 * Method returns if there was some error during the execution of the composer or not.
	 * 
	 * @return boolean True if some error happened, False otherwise
	 */
	public function hasError() {
		return $this->_errorOccured;
	}

	/**
	 * Read the list of errors which might have occured during the execution.
	 * 
	 * @return array Array of error messages
	 */
	public function getErrors() {
		return $this->_errors;
	}

	/**
	 * Socket request that downloads composer.phar file using correct socket configuration.
	 * 
	 * @param  string $url  Composer URL
	 * @param  string $path File path to download to
	 * @return HttpSocket
	 */
	protected function _download($url, $path) {
		$ErambaHttpComponent = new ErambaHttpComponent(new ComponentCollection(), []);

		$file = fopen($path, 'w');
		$config = $ErambaHttpComponent->config;
		$config['timeout'] = 60;

		$http = new ErambaHttpSocket($config);
		$http->setContentResource($file);

		$download = $http->get($url);
		fclose($file);

		return $download;
	}

	/**
	 * Download composer.phar package.
	 * 
	 * @return string Path to the PHAR package
	 */
	public function getComposer() {
		$path = $this->_workingDir . '/' . $this->_phar;
		if (!is_file($path)) {
			$this->out(__('Downloading composer.phar file'));
			$download = $this->_download('https://getcomposer.org/composer.phar', $path);

			if ($download === false) {
				$this->err(__('Error occured while downloading composer.phar file.'));
			}
			else {
				$this->out(__('File composer.phar successfully downloaded.'));
			}
		}
		else {
			$this->out(__('File composer.phar already exists, skipping download.'));
		}

		return $path;
	}

	/**
	 * Setup Composer's environmental values if necessary.
	 * 
	 * @return void
	 */
	protected function _configure() {
		require_once "phar://{$this->_workingDir}/{$this->_phar}/src/bootstrap.php";

		if (!getenv('HOME') && !getenv('COMPOSER_HOME')) {
			putenv("COMPOSER_HOME=" . $this->_workingDir);
		}

		putenv("OSTYPE=OS400"); //force to use php://output instead of php://stdout
	}

	/**
	 * Get the array of arguments applicable for configuring application's Composer libraries.
	 * 
	 * @return array Array of arguments
	 */
	protected function _getInputArgs($cmd) {
		$args = [
			'command' => $cmd,
		];

		// for debug mode we let composer install also --dev packages
		if (!Configure::read('debug')) {
			$args['--no-dev'] = true;
		}

		return $args;
	}

	/**
	 * Get the input to use when executing composer command.
	 * 
	 * @return ArrayInput
	 */
	public function getInput($cmd) {
		$args = $this->_getInputArgs($cmd);

		return new \Symfony\Component\Console\Input\ArrayInput($args);
	}

	/**
	 * Create the application and run it with the commands.
	 * 
	 * @return int Exit code of the command.
	 */
	protected function _executeCmd($cmd) {
		$application = new \Composer\Console\Application();
		$application->setAutoExit(false);
		$application->setCatchExceptions(false);

		// $factory = new \Composer\Factory();
		// $output = $factory->createOutput();

		try {
			$input = $this->getInput($cmd);
			$input->setInteractive(false);

			ob_start();
			$exitCode = $application->run($input);
			ob_end_clean();
		}
		catch (\Exception $e) {
			$exitCode = 1;
			$this->_setError($e->getMessage);
		}

		if ($exitCode == 0) {
			$this->_errorOccured = false;
		}
		elseif ($exitCode == 2) {
			$this->_setError(__('Composer Failed due to dependency solving error'));
		}
		else {
			$this->_setError(__('Composer Failed due to generic error'));
		}
	}

	/**
	 * Internal wrapper method for executing a composer command.
	 * 
	 * @param  string $cmd Name of the command
	 * @return void
	 */
	protected function _execute($cmd = 'update') {
		$this->out(__('Executing %s command with composer.phar package', $cmd));

		$this->_reset();

		// prepare composer.phar package
		$this->getComposer();

		// configure composer for use in the application
		$this->_configure();

		$cwd = getcwd();

		// change the working directory to /upgrade where composer.json is located
		chdir($this->_workingDir);

		// finally execute desired composer ccommand
		$this->_executeCmd($cmd);

		chdir($cwd);
	}

	/**
	 * Toggles the update process of a Composer in the current application.
	 * 
	 * @return void
	 */
	public function update() {
		$this->_execute('update');
	}
	
	/**
	 * Toggles the install process of a Composer in the current application.
	 * 
	 * @return void
	 */
	public function install() {
		$this->_execute('install');
	}
}