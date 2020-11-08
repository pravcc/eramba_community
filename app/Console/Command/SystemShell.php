<?php
/**
 * Acl Dashboard Shell.
 */
App::uses('AppShell', 'Console/Command');
App::uses('SystemHealthLib', 'Lib');
App::uses('ClassRegistry', 'Utility');
App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('AppComposer', 'Utility');
App::uses('NewTemplateDbMigration', 'Lib');

/**
 * Shell for Dashboard ACOs
 *
 * @package		Dashboard.Console.Command
 */
class SystemShell extends AppShell {

/**
 * Contains arguments parsed from the command line.
 *
 * @var array
 * @access public
 */
	public $args;

/**
 * SystemHealthLib instance
 */
	public $SystemHealthLib;

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

	public function new_template_db_migration()
	{
		$NewTemplateDbMigration = new NewTemplateDbMigration();

		$ret = $NewTemplateDbMigration->run();
		if ($ret) {
			$this->out("New Template's DB migration completed successfully.");
		}
		else {
			$this->out("<error>Error(s) occured while migrating new templates database</error>");
		}

		return $ret;
	}

	public function migrate_db() {
		$this->_loginAdmin();
		
		$Setting = ClassRegistry::init([
			'table' => false,
			'ds' => 'default',
			'alias' => 'Setting',
			'name' => 'Setting',
			'class' => 'Setting'
		]);
		$ret = $Setting->runMigrations();
		
		if ($ret) {
			$this->out("Database migration completed successfully.");
		}
		else {
			$this->out("<error>Error(s) occured while migrating your database</error>");
		}

		return $ret;
	}

	public function reset_db() {
		// $this->_loginAdmin();
		
		$Setting = ClassRegistry::init([
			'table' => false,
			'ds' => 'default',
			'alias' => 'Setting',
			'name' => 'Setting',
			'class' => 'Setting'
		]);
		$ret = $Setting->resetDatabase(false);
		
		if ($ret) {
			$this->out("Database reset.");
		}
		else {
			$this->out("<error>Error(s) occured while resetting your database, blame martin</error>");
		}
	}

	public function sync_db() {
		$ret = true;
		
		$AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');

		// we remove all system filters before synchronizing new ones
		$ret &= $AdvancedFilter->deleteAll([
			'AdvancedFilter.system_filter' => 1
		]);

		// and we sync new system filters
		$ret &= $AdvancedFilter->syncDefaultIndex();
		
		if ($ret) {
			$this->out("Additional database sync completed successfully.");
		}
		else {
			$this->out("<error>Error(s) occured while doing an additional database sync</error>");
		}

		return $ret;
	}

	public function delete_cache() {
		return $this->dispatchShell('update', 'deleteCache');
	}

	public function composer() {
		$ret = true;

		if (empty($this->args)) {
			$this->error(__('Argument for this command is missing. Use either composer update or composer install to proceed.'));
		}

		$AppComposer = new AppComposer();
		$AppComposer->Shell = $this;
		
		$cmd = $this->args[0];

		if (!in_array($cmd, ['install', 'update'])) {
			$this->error('Invalid command to execute in composer.');
		}

		$AppComposer->{$cmd}();
		if (!$AppComposer->hasError()) {
			$this->out("Composer command 'composer {$cmd}' completed successfully.");
		}
		else {
			$this->out("<error>Error(s) occured while running a composer:</error>");
			$this->out($AppComposer->getErrors());
			$this->error('');
		}
	}

	public function getOptionParser() {
		return parent::getOptionParser()
			->description(__("System Shell"))
			->addSubcommand('check', [
				'help' => __('Console version of system health check.') . PHP_EOL .
						  __('Verbose (-v) shows description for each check, otherwise only failed checks have them.')
			])
			->addSubcommand('delete_cache', [
				'help' => __('Clean all cache.')
			])
			->addSubcommand('composer', [
				'help' => __('Run composer update, or composer install.')
			])
			->addSubcommand('migrate_db', [
				'help' => __('Database migration using Phinx - the same migration tool as the one that runs during update process.')
			])
			->addSubcommand('reset_db', [
				'help' => __('Native reset database command. Use with caution!')
			])
			->addSubcommand('sync_db', [
				'help' => __('Additional database synchronization of data.')
			])
			->addSubcommand('new_template_db_migration', [
				'help' => __('DB migration specifically for updates to new template.')
			]);;
	}

}
