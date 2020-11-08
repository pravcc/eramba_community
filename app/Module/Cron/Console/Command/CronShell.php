<?php
/**
 * CRON Shell.
 */
App::uses('AppShell', 'Console/Command');
App::uses('ClassRegistry', 'Utility');
App::uses('CronModule', 'Cron.Lib');
App::uses('Cron', 'Model');
App::uses('CronTask', 'Cron.Model');
App::uses('CrudSubject', 'Crud.Controller/Crud');
App::uses('CakeEvent', 'Event');
App::uses('AppErrorHandler', 'Error');
App::uses('CronException', 'Cron.Error');
App::uses('CakeNumber', 'Utility');
App::uses('CakeLog', 'Log');
App::uses('Validation', 'Utility');

/**
 * Shell for Crons
 *
 * @package		Cron.Console.Command
 */
class CronShell extends AppShell
{

	/**
	 * Contains arguments parsed from the command line.
	 *
	 * @var array
	 * @access public
	 */
	public $args;

	/**
	 * Cron model.
	 * @var Object
	 */
	public $Cron;

	/**
	 * Constructor
	 */
	public function __construct($stdout = null, $stderr = null, $stdin = null)
	{
		parent::__construct($stdout, $stderr, $stdin);
	}

	/**
	 * Start up.
	 *
	 * @return void
	 **/
	public function startup()
	{
		parent::startup();

		$this->Cron = ClassRegistry::init('Cron');
	}

	/**
	 * Test a CRON Job.
	 *
	 * @return void
	 */
	public function test()
	{	
		if (Configure::read('Eramba.Settings.CRON_TYPE') !== 'cli') {
			$this->out('<error>You have to configure CLI Cron Type in Settings of Eramba in order to use this.</error>');
		} else {
			$this->out('<success>All looks OK.</success>');
		}
	}

	/**
	 * Executes a CRON Job.
	 *
	 * @return void
	 */
	public function job()
	{	
		if (Configure::read('Eramba.Settings.CRON_TYPE') !== 'cli') {
			$this->out('<error>You have to configure CLI Cron Type in Settings of Eramba in order to use this.</error>');
			return true;
		}

		$possibleTypes = [
			'hourly',
			'daily',
			'yearly'
		];

		$conds = !isset($this->args[0]);
		$conds = $conds || !in_array($this->args[0], $possibleTypes);
		if ($conds) {
			$this->error('Please provide a type of the CRON (' . implode('/', $possibleTypes) . ')');
		}

		$type = $this->args[0];

		$this->_loginAdmin();

		try {
			$this->_job($type);
		} catch (CronException $e) {
			$message = $e->getMessage();
			$this->err($message);
			// $this->_saveCronRecord($type, false, $message);
		}

		// $this->_saveCronRecord($type, true, $message);
	}

	/**
	 * Internal wrapper to execute a CRON job type.
	 * 
	 * @param  string $type Type of the CRON.
	 * @return boolean
	 */
	protected function _job($type)
	{
		$valid = $this->_validateCron($type);
		if (!$valid) {
			throw new CronException(__("Your request to execute a %s CRON job is invalid, either it has been processed already or it is still being processed", $type));
		}

		$data = array(
			'type' => $type,
			// 'execution_time' => scriptExecutionTime(),
			'status' => Cron::STATUS_PENDING,
			// 'request_id' => self::requestId(),
			// 'url' => Router::fullBaseUrl(),
			'message' => __('Your CRON Job is running in the background. Status can be viewed on the CRON index.')
		);

		$this->Cron->create();
		$this->Cron->set($data);
		$ret = $this->Cron->save(null, false);
		$cronId = $this->Cron->id;

		$ret = $this->_runTasks($type, $cronId);

		$this->Cron->summarizeCron($cronId);

		if ($ret) {
			$this->out('<success>Cron successfully executed.</success>');
		} else {
			$this->error('Error occured while running Cron');
		}
	}

	/**
	 * Summarizes result for the cron provided as argument.
	 * 
	 * @param  int $cronId  Cron ID.
	 */
	protected function _saveFinalCronRecord($cronId)
	{

	}

	/**
	 * Validate a type of Cron, if it can be run or not.
	 * 
	 * @param  string $type Cron type.
	 * @return boolean      True if cron can be ran, False otherwise.
	 */
	protected function _validateCron($type)
	{
		$ret = true;

		$ret &= !$this->Cron->cronTaskExists($type);
		$ret &= !$this->Cron->isCronTaskRunning($type);

		return $ret;
	}

	/**
	 * Get the list of Cron Tasks for a given type.
	 * 
	 * @param  string $type Cron Type.
	 * @return array        List of tasks as array.
	 */
	protected function _getTasks($type)
	{
		return CronModule::$jobs[$type];
	}

	/**
	 * Method process all tasks within a single type of cron.
	 * 
	 * @param  string $type   Type of CRON.
	 * @param  int    $cronId ID of the cron.
	 * @return boolean        True on success, false otherwise.
	 */
	protected function _runTasks($type, $cronId)
	{
		$tasks = $this->_getTasks($type);

		$success = true;
		foreach ($tasks as $task => $name) {
			$taskStatus = CronTask::STATUS_SUCCESS;
			$this->out('Starting Cron Task - ' . $name);

			$beginTime = microtime(true);
			try {
				$this->_executeTask($type, $name, $cronId);

				$message = __('Your CRON Task "%s" has been completed successfully', $name);
				// $this->out('Successfully completed task - ' . $name);
			} catch (Exception $e) {
				$taskStatus = CronTask::STATUS_ERROR;
				$message = __('CRON task "%s" failed to process with error: %s', $name, $e->getMessage());
				// $this->out('<error>Error occured while running task - ' . $name . '</error>');

				// log the error
				AppErrorHandler::logException($e);
				$success = false;
			}

			$endTime = microtime(true);
			$duration = CakeNumber::precision($endTime - $beginTime);

			$this->_saveTask($cronId, $name, $taskStatus, $duration, $message);
		}

		return $success;
	}
	
	/**
	 * Store a task that has been processed within a cron into database.
	 * @param  [type] $cronId   [description]
	 * @param  [type] $name     [description]
	 * @param  [type] $duration [description]
	 * @param  [type] $message  [description]
	 * @return [type]           [description]
	 */
	protected function _saveTask($cronId, $name, $status, $duration, $message)
	{
		$this->Cron->CronTask->create();
		$this->Cron->CronTask->set([
			'cron_id' => $cronId,
			'task' => $name,
			'execution_time' => $duration,
			'created' => CakeTime::format('Y-m-d H:i:s', CakeTime::fromString('-' . floor($duration) . ' seconds')),
			'completed' => CakeTime::format('Y-m-d H:i:s', CakeTime::fromString('now')),
			'status' => $status,
			'message' => $message
		]);

		if ($status == CronTask::STATUS_ERROR) {
			$outStatus = '<error>Error</error>';
		} else {
			$outStatus = '<success>Success</success>';
		}

		$this->out('<info>Task:</info> ' . $name);
		$this->out('<info>Status:</info> ' . $outStatus);
		$this->out('<info>Message:</info> ' . $message);
		$this->out('<info>Execution Time:</info> ' . $duration . 's');

		$this->hr();

		$log = 'Cron task has been finished:' . PHP_EOL;		
		$log .= 'Task: ' . $name . PHP_EOL;
		$log .= 'Status: ' . CronTask::statuses($status) . PHP_EOL;
		$log .= 'Message: ' . $message . PHP_EOL;
		$log .= 'Execution Time: ' . $duration . PHP_EOL;

		CakeLog::write('Cron', $log);

		return (bool) $this->Cron->CronTask->save();
	}

	/**
	 * Execute a single Cron task.
	 * 
	 * @param  string $name Name of the task.
	 */
	protected function _executeTask($type, $name, $cronId)
	{
		$CrudSubject = new CrudSubject();
		$CrudSubject->cronId = $cronId;

		list($plugin, $name) = pluginSplit($name, true);

		$name = $name . 'Listener';
		App::uses($name, $plugin . 'Controller/Crud/Listener');
		$TaskClass = new $name($CrudSubject);
		
		$Event = new CakeEvent('Cron.' . $type, $CrudSubject);
		
		$TaskClass->beforeHandle($Event);
		$TaskClass->{$type}($Event);
	}


	/**
	 * Configure OptionParser.
	 */
	public function getOptionParser()
	{
		return parent::getOptionParser()
			->description(__("CRON Shell Manager"))
			->addSubcommand('test', array(
				'help' => __('Test command to check CLI access and some basics for using Cron jobs in shell.'),
				/*'parser' => array(
					'options' => array(
						'full-base-url' => array(
							'short' => 'f',
							// 'required' => true,
							'help' => __('Full Base URL to correctly format links built within CLI, for example links in emails, etc. Configure here the URL you use to browse eramba, otherwise http://localhost is used as default value.'),
							// 'choices' => array('hourly', 'daily', 'yearly')
						)
					)
				)*/
			))
			->addSubcommand('job', array(
				'help' => __('Execute any type of CRON Job'),
				'parser' => array(
					'arguments' => array(
						'type' => array(
							'required' => true,
							'help' => __('The type of CRON job to run'),
							'choices' => array('hourly', 'daily', 'yearly')
						)
					),
					/*'options' => array(
						'full-base-url' => array(
							'short' => 'f',
							'help' => __('Full Base URL to correctly format links built within CLI, for example links in emails, etc. Configure here the URL you use to browse eramba, otherwise http://localhost is used as default value.')
						)
					)*/
				)
			));
	}

}
