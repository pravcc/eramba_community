<?php
App::uses('AppShell', 'Console/Command');
App::uses('DbAcl', 'Model');
App::uses('CacheDbAcl', 'Lib');
App::uses('Hash', 'Utility');
App::uses('File', 'Utility');
App::uses('AppErrorHandler', 'Error');
App::uses('AutoUpdateLib', 'Lib');

class UpdateShell extends AppShell {

	public function startup() {
		parent::startup();
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(
				'Updates Shell Helper.' .
				'')
			->addSubcommand('update', array(
				'help' => __('Update command that processes a package for your current installation.')))
			->addSubcommand('dumpSchema', array(
				'help' => __('Dump the latest possible schema, requires a different datasource (clean_database) configured in the app.')))
			->addSubcommand('deploy', array(
				'help' => __('Deploy fresh database on your current datasource. Drops all existing tables, runs migrations to the latest database. Will reset your CLIENT ID if you have one defined already.')))
			->addSubcommand('reset', array(
				'help' => __('Same as deploy but will keep your CLIENT ID.')))
			->addSubcommand('syncAcl', array(
				'help' => __('ACL synchronization.')))
			->addSubcommand('deleteCache', array(
				'help' => __('Deletes cache.')))
			->addSubcommand('cleanup', array(
				'help' => __('ACL Sync and delete cache both.')));
	}

	public function dumpSchema()
	{
		App::uses('Model', 'Model');
		App::uses('BackupDatabaseLib', 'BackupRestore.Lib');

		// load datasource that handles dumping clean schema
		$modelConfig = ['table' => false, 'name' => 'Setting', 'alias' => 'UpdateSetting', 'ds' => 'clean_database'];
		$Setting = new Setting($modelConfig);

		$UpdateSetting = ClassRegistry::init('UpdateSetting');

		// reset database in the alternate datasource to get the last possible current schema
		$ret = $UpdateSetting->resetDatabase(false);
		if ($ret) {
			// build clean sql file out of the database dumped in the alternate datasource
			$BackupDatabaseLib = new BackupDatabaseLib();
			$BackupDatabaseLib->ds = 'clean_database';
			$BackupDatabaseLib->build(APP . 'Config/db_schema/' . Configure::read('Eramba.version') . '-clean.sql', true);
		}
	}

	public function update()
	{
		if (!isset($this->args[0])) {
            $this->error('Please provide a udpate file path.');
        }

        $path = $this->args[0];

        $file = new File($path);

        if (!$file->exists()) {
        	$this->error('Update package file does not exist. Please try again.');
        }

        if (!$file->readable()) {
        	$this->error('Update package file is not readable. Please try again.');
        }

        $this->_updateCleanup();

        try {
			$phar = new PharData($path);
			$phar->extractTo(TMP, ['VERSION']);
		} catch (Exception $e) {
			// log the error
			AppErrorHandler::logException($e);

			$this->error('Error occured when opening the package file.');
		}

		$VersionFile = new File(TMP . 'VERSION');
		$toVersion = $VersionFile->read();

		$choice = strtolower($this->in(sprintf('You are trying to update from version %s to %s, are you sure you want to proceed?', Configure::read('Eramba.version'), $toVersion), array('Y', 'N')));
		switch ($choice) {
			case 'y':
				$this->_loginAdmin();
				$this->_initUpdateProcess($path);
				break;
			case 'n':
				return $this->_stop();
				// $this->out($this->OptionParser->help());
				break;
			case 'q':
				return $this->_stop();
			default:
				$this->out('You have made an invalid selection. Please choose a command to execute by entering Y or N.');
		}
	}

	protected function _initUpdateProcess($file)
	{
		$this->hr();
		$this->out('Starting update process ...');

		$AutoUpdateLib = new AutoUpdateLib();
		$AutoUpdateLib->Shell = $this;

		$AutoUpdateLib->update($file);

		if ($AutoUpdateLib->hasError()) {
			$this->error($AutoUpdateLib->getErrorMessage());
		}
		else {
			$this->out('<success>Successfuly updated.</success>');
		}
	}

	/**
	 * Cleanup for shell update process.
	 */
	protected function _updateCleanup()
	{
		$VersionFile = new File(TMP . 'VERSION');
		$VersionFile->delete();
	}

	public function drop_tables() {
		if(ClassRegistry::init('Setting')->dropAllTables()){
			$this->out('All tables removed.');
		}
		else {
			$this->error('Error occured!');
		}
	}

	public function deploy() {
		$this->out('Deploying fresh database...');

		$options = array(
			'name' => 'Setting', 'class' => 'Setting', 'table' => false, 'ds' => 'default', 'alias' => 'Setting'
		);

		$m = ClassRegistry::init($options);
		$m->useTable = false;
		
		$dataSource = $m->getDataSource();
		$dataSource->begin();

		$ret = $m->resetDatabase(false);
		
		if($ret){
			$this->out('Deployed successfully! Your app will get registered with a new Client ID upon first login.');
			$dataSource->commit();
		}
		else {
			$this->error('Error occured while trying to deploy a fresh installation!');
			$dataSource->rollback();
		}
	}

	public function reset() {
		$this->out('Reseting database...');

		$dataSource = ClassRegistry::init('Setting')->getDataSource();
		$dataSource->begin();

		$ret = ClassRegistry::init('Setting')->resetDatabase(true);

		if($ret){
			$this->out('Reset done! Your application registration remained unchanged.');
			$dataSource->commit();
		}
		else {
			$this->error('Error occured!');
			$dataSource->rollback();
		}
	}

	public function cleanup() {
		$this->syncAcl();
		$this->deleteCache();
	}

	public function syncAcl() {
		$this->out('Syncing ACL...');

		ClassRegistry::init('Setting')->syncAcl();

		$this->out('ACL in sync!');
	}

	public function deleteCache() {
		$this->out('Deleting cache...');

		$cache = ClassRegistry::init('Setting')->deleteCache('');

        $this->out($cache ? 'Cache removed' : 'Cache partially removed');
    }

    public function system_hash() {
    	App::uses('Folder', 'Utility');
		App::uses('File', 'Utility');
    	$this->out(ClassRegistry::init('Setting')->getIntegrityHash());
    }

    public function hey_there() {
        $this->out('Hey there ' . $this->args[0]);
    }
}