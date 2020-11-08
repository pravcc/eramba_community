<?php
/**
 * @package       Workflows.Model
 */

App::uses('WorkflowsAppModel', 'Workflows.Model');
App::uses('WorkflowAccess', 'Workflows.Model');
App::uses('Inflector', 'Utility');

class WorkflowAccessType extends WorkflowsAppModel {
	public $useTable = 'wf_access_types';

	public $actsAs = array(
		'Containable'
	);

	/*
	 * Types of objects that give approvals.
	 * @access static
	 */
	 public static function types($value = null) {
		$options = array(
			self::TYPE_USER => __('User'),
			self::TYPE_GROUP => __('Group'),
			self::TYPE_CUSTOM => __('Custom Role')
		);
		return parent::enum($value, $options);
	}
	const TYPE_USER = 'user';
	const TYPE_GROUP = 'group';
	const TYPE_CUSTOM = 'custom_role';

	// protected static $_assocInstances = [];

	/**
	 * Resolves users with specific access.
	 * 
	 * @param  string $type       Access Type.
	 * @param  string $foreignKey Foreign key for the Access Type.
	 * @return array              User IDs.
	 */
	public function processType($type, $foreignKey) {
		$cacheStr = '_parsed_single_type_'. $type . '_' . $foreignKey;

		if (($data = Cache::read($cacheStr, 'workflows_access')) === false) {
			$className = self::classifyType($type);
			App::uses($className, 'Workflows.Lib/AccessTypes');

			$data = (new $className([]))->process($foreignKey, null);
			WorkflowAccess::uniqueify($data);

			Cache::write($cacheStr, $data, 'workflows_access');
		}
		
		return $data;
	}

	public static function classifyType($type) {
		return Inflector::classify($type) . 'AccessType';
	}

	// public static function getInstance($type) {
	// 	$className = self::classifyType($type);

	// 	if (!isset(self::$_assocInstances[$className])) {
	// 		App::uses($className, 'Workflows.Lib/AccessTypes');
	// 		$instance = new $className(1);
	// 		self::$_assocInstances[$className] = $instance;
	// 	}

	// 	return self::$_assocInstances[$className];
	// }

}