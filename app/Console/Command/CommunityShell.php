<?php
/**
 * Acl Dashboard Shell.
 */
App::uses('AppShell', 'Console/Command');

/**
 * Shell for Community migration
 */
class CommunityShell extends AppShell {

/**
 * Contains arguments parsed from the command line.
 *
 * @var array
 * @access public
 */
	public $args;

/**
 * Constructor
 */
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
	}

/**
 * Start up And load Acl Component / Aco model
 *
 * @return void
 **/
	public function startup() {
		parent::startup();
	}

	public function migrate() {
		App::uses('NewTemplateDbMigration', 'Lib');

		$ret = $this->dispatchShell('system', 'migrate_db');
		$ret &= NewTemplateDbMigration::additionalDbSync();
		$this->dispatchShell('update', 'deleteCache');

		if ($ret) {
			$this->out("All ok, database migrated.");
		}
		else {
			$this->out("<error>Error(s) occured while migrating your database.</error>");
		}
	}

	public function getOptionParser() {
		return parent::getOptionParser()
			->description(__("Community Shell"))
			->addSubcommand('migrate', [
				'help' => __('Migrate to the latest database version')
			]);
	}

}
