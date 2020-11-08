<?php
/**
 * CRON Shell.
 */
App::uses('AppShell', 'Console/Command');
App::uses('ClassRegistry', 'Utility');
App::uses('SystemHealthLib', 'Lib');

/**
 * Shell for System Health
 *
 * @package		Console.Command
 */
class SystemHealthShell extends AppShell
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

		$this->SystemHealthLib = new SystemHealthLib();
	}

	/**
	 * CLI System Health Check
	 *
	 * @return void
	 */
	public function check()
	{
		$data = $this->SystemHealthLib->getData();

		foreach ($data as $group) {
			$this->out('<warning>Group:</warning> ' . $group['groupName']);
			$this->hr();
			$errors = 0;
			foreach ($group['checks'] as $check) {
				if (!$check['status']) {
					$errors++;
				}

				if ($this->param('errors-only') && $check['status']) {
					continue;
				}

				if ($check['status']) {
					$outStatus = '<success>OK</success>';
				} else {
					$outStatus = '<error>Error</error>';
				}

				$this->out('<info>Name:</info> ' . $check['name']);
				$this->out('<info>Description:</info> ' . $check['description']);
				$this->out('<info>Status:</info> ' . $outStatus);
				$this->hr();
			}
		}

		// show all success if 'errors-only' is enabled and there are no errors, to at least show something
		if ($this->param('errors-only') && $errors == 0) {
			$this->out('<success>System Health appears OK</success>');
		}
	}

	/**
	 * Configure OptionParser.
	 */
	public function getOptionParser()
	{
		return parent::getOptionParser()
			->description(__("CRON Shell Manager"))
			->addSubcommand('check', array(
				'help' => __('Check System Health via CLI.'),
				'parser' => array(
					'options' => array(
						'errors-only' => array(
							'short' => 'e',
							'help' => __('Show only errors in System Health.'),
							'boolean' => true
						)
					)
				)
			));
	}

}
