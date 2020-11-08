<?php
App::uses('AppShell', 'Console/Command');
App::uses('AdvancedFiltersModule', 'AdvancedFilters.Lib');

/**
 * Shell for AdvancedFilters
 *
 * @package	AdvancedFilters.Console.Command
 */
class AdvancedFiltersShell extends AppShell
{
	/**
	 * Contains arguments parsed from the command line.
	 *
	 * @var array
	 * @access public
	 */
	public $args;

	/**
	 * AdvancedFiltersModule instance
	 */
	public $AdvancedFiltersModule;

	/**
	 * Constructor
	 */
	public function __construct($stdout = null, $stderr = null, $stdin = null)
	{
		parent::__construct($stdout, $stderr, $stdin);
		$this->AdvancedFiltersModule = new AdvancedFiltersModule();
	}

	public function migrate_filter_args()
	{
		$ret = true;

		$this->out('Advanced Filter args synchronization started...');

		$ret &= $this->AdvancedFiltersModule->migrateFilterArgs();

		if ($ret) {
            $this->out('All args have been updated successfully.');
        }
        else {
            $this->error('Something went wrong. Args have not been updated successfully.');
        }

		return $ret;
	}

	public function getOptionParser()
	{
		return parent::getOptionParser()
			->description(__('AdvancedFilters shell helper manager'))
			->addSubcommand('migrate_filter_args');
	}

}
