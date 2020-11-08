<?php
/**
 * @package       Workflows.Lib
 */

App::uses('ModuleBase', 'Lib');
class WorkflowsModule extends ModuleBase {
	public $toolbar = true;

	protected $_whitelist = [
		'Risk'
	];

	public function __construct() {
		$this->name = __('Workflows');

		parent::__construct();
	}

	public function getSectionUrl($model) {
		return [
			'plugin' => 'workflows',
			'controller' => 'workflowStages',
			'action' => 'index',
			$model
		];
	}

	public function getItemUrl($model, $foreignKey) {
		return [
			'plugin' => 'workflows',
			'controller' => 'workflowInstances',
			'action' => 'manage',
			$model,
			$foreignKey
		];
	}
}
