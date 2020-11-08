<?php
/**
 * Acl Visualisation Shell.
 */
App::uses('AppShell', 'Console/Command');
App::uses('Visualisation', 'Visualisation.Lib');
App::uses('CustomRoles', 'CustomRoles.Lib');
App::uses('ClassRegistry', 'Utility');

/**
 * Shell for Visualisation ACOs
 *
 * @package		Visualisation.Console.Command
 */
class VisualisationShell extends AppShell {

/**
 * Contains arguments parsed from the command line.
 *
 * @var array
 * @access public
 */
	public $args;

/**
 * Visualisation instance
 */
	public $Visualisation;

/**
 * CustomRoles instance
 */
	public $CustomRoles;

/**
 * Constructor
 */
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
		$this->Visualisation = new Visualisation();
		$this->CustomRoles = new CustomRoles();
	}

/**
 * Start up And load Acl Component / Aco model
 *
 * @return void
 **/
	public function startup() {
		Configure::write('Cache.disable', true);
		
		parent::startup();
		$this->Visualisation->startup();
		$this->Visualisation->Shell = $this;

		$this->CustomRoles->startup();
		$this->CustomRoles->Shell = $this;
	}

	public function compliance_sync() {
		$this->_loginAdmin();
		$Compliance = ClassRegistry::init('ComplianceManagement');

		return $Compliance->syncObjects();
	}

/**
 * Sync the ACO table
 *
 * @return void
 **/
	public function acl_sync() {
		$ret = true;
		
		ClassRegistry::init("Setting")->deleteCache(null);

		if ($this->param('interactive')) {
			$response = $this->in(__('Perform a ACL sync for sections and it\'s objects? (y)'), ['y', 'n'], 'y');
			if (in_array(strtolower($response), ['y', 'yes'])) {
				$ret &= $this->Visualisation->sync_objects($this->params);
			}

			$response = $this->in(__('Perform a ACL sync for users? (y)'), ['y', 'n'], 'y');
			if (in_array(strtolower($response), ['y', 'yes'])) {
				$ret &= $this->Visualisation->sync_users($this->params);
			}

			$response = $this->in(__('Perform a ACL sync for custom roles? (y)'), ['y', 'n'], 'y');
			if (in_array(strtolower($response), ['y', 'yes'])) {
				$ret &= $this->CustomRoles->sync($this->params);
			}
		}
		else {
			$ret &= $this->Visualisation->sync_objects($this->params);
			$ret &= $this->Visualisation->sync_users($this->params);
			$ret &= $this->CustomRoles->sync($this->params);
		}

		$ret &= $this->Visualisation->permissions();

		return $ret;
	}

	public function getOptionParser() {
		$plugin = array(
			'short' => 'p',
			'help' => __('Plugin to process'),
		);
		$interactive = array(
				'help' => __('Interactive'),
				'short' => 'i',
				'boolean' => true,
				// 'default' => false
			);
		
		return parent::getOptionParser()
			->description(__("Visualisation shell helper manager"))
			->addSubcommand('compliance_sync')
			->addSubcommand('acl_sync', array(
				'parser' => array(
					'options' => compact('interactive'),
					),
				'help' => __('Perform a full sync on the ACL tables.' .
					'Will create new ACOs or missing models and objects.' .
					'Will also remove orphaned entries that no longer have a matching model/object' . 
					'Also Custom Roles and User objects')
			))->addOption('interactive', array(
				'help' => __('Interactive'),
				'short' => 'i',
				'boolean' => true,
				// 'default' => false
			));
	}

}
