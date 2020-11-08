<?php
App::uses('AppShell', 'Console/Command');
App::uses('ObjectStatusModule', 'ObjectStatus.Lib');

/**
 * Shell for ObjectStatus
 *
 * @package	ObjectStatus.Console.Command
 */
class ObjectStatusShell extends AppShell {

/**
 * Contains arguments parsed from the command line.
 *
 * @var array
 * @access public
 */
	public $args;

/**
 * ObjectStatusModule instance
 */
	public $ObjectStatusModule;

/**
 * Constructor
 */
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
		$this->ObjectStatusModule = new ObjectStatusModule();
	}

/**
 * Sync all object status data.
 *
 * @return void
 **/
	public function sync_all_statuses() {
		$ret = true;

		$this->out('Statuses synchronization started...');

		$ret &= $this->ObjectStatusModule->syncAllStatuses();

		if ($ret) {
            $this->out('All statuses have been updated successfully.');
        }
        else {
            $this->error('Something went wrong. Statuses have not been updated successfully.');
        }

		return $ret;
	}

	public function getOptionParser() {
		return parent::getOptionParser()
			->description(__('ObjectStatus shell helper manager'))
			->addSubcommand('sync_all_statuses');
	}

}
