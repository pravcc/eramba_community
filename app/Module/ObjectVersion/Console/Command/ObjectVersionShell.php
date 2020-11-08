<?php
/**
 * Acl Visualisation Shell.
 */
App::uses('AppShell', 'Console/Command');

/**
 * Shell for Visualisation ACOs
 *
 * @package		Visualisation.Console.Command
 */
class ObjectVersionShell extends AppShell {

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

	public function getOptionParser() {
		return parent::getOptionParser()
			->description(__("Object Versioning"));
	}

	public function add_versioning() {
		$this->_loginAdmin();

		$this->out('Revision for objects history synchronization started...');

		$V = ClassRegistry::init('ObjectVersion.ObjectVersion');
		$ret = $V->addMissingVersioning();

		if ($ret) {
			$this->out('Revision for history added to all objects that were missing it.');
		}
		else {
			$this->error('History object revisions was not added successfully for those missing it.');
		}

		return $ret;
	}

}
