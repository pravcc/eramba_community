<?php
App::uses('AppShell', 'Console/Command');
App::uses('UserFieldsModule', 'UserFields.Lib');

/**
 * Shell for UserFields
 *
 * @package	UserFields.Console.Command
 */
class UserFieldsShell extends AppShell
{
	/**
	 * Contains arguments parsed from the command line.
	 *
	 * @var array
	 * @access public
	 */
	public $args;

	/**
	 * UserFieldsModule instance
	 */
	public $UserFieldsModule;

	/**
	 * Constructor
	 */
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
		$this->UserFieldsModule = new UserFieldsModule();
	}

	/**
	 * Sync existing objects.
	 *
	 * @return boolean
	 **/
	public function sync_existing_objects() {
		$ret = true;

		$this->out('User field objects synchronization started...');

		$ret &= $this->UserFieldsModule->syncExistingObjects();

		if ($ret) {
            $this->out('All user field objects have been created successfully.');
        }
        else {
            $this->error('Something went wrong. User field objects have not been created successfully.');
        }

		return $ret;
	}

	public function getOptionParser() {
		return parent::getOptionParser()
			->description(__('UserFields shell helper manager'))
			->addSubcommand('sync_existing_objects');
	}

}
