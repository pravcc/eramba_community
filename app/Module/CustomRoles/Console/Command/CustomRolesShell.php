<?php
/**
 * Acl Visualisation Shell.
 */
App::uses('AppShell', 'Console/Command');
App::uses('CustomRoles', 'CustomRoles.Lib');

/**
 * Shell for Visualisation ACOs
 *
 * @package		Visualisation.Console.Command
 */
class CustomRolesShell extends AppShell {

/**
 * Contains arguments parsed from the command line.
 *
 * @var array
 * @access public
 */
	public $args;

/**
 * CustomRoles instance
 */
	public $CustomRoles;

/**
 * Constructor
 */
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
		$this->CustomRoles = new CustomRoles();
	}

/**
 * Start up And load Acl Component / Aco model
 *
 * @return void
 **/
	public function startup() {
		parent::startup();

		$this->CustomRoles->startup();
		$this->CustomRoles->Shell = $this;
	}

	public function sync() {
		parent::startup();

		$ret = $this->CustomRoles->sync($this->params);

		return $ret;
	}

	public function getOptionParser() {
		$plugin = array(
			'short' => 'p',
			'help' => __('Plugin to process'),
		);
		
		return parent::getOptionParser()
			->description(__("Custom Roles Shell"))
			->addSubcommand('sync');
	}

}
