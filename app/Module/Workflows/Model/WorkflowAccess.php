<?php
/**
 * @package       Workflows.Model
 */

App::uses('WorkflowsAppModel', 'Workflows.Model');
App::uses('WorkflowAccessType', 'Workflows.Model');
App::uses('WorkflowAccessObject', 'Workflows.Lib');

class WorkflowAccess extends WorkflowsAppModel {
	public $useTable = 'wf_accesses';

	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'WorkflowAccessModel' => [
			'className' => 'Workflows.WorkflowAccessModel',
			'foreignKey' => false,
			'conditions' => [
				'WorkflowAccessModel.name = WorkflowAccess.wf_access_model'
			]
		],
		'WorkflowAccessType' => [
			'className' => 'Workflows.WorkflowAccessType',
			'foreignKey' => false,
			'conditions' => [
				'WorkflowAccessType.slug = WorkflowAccess.wf_access_type'
			]
		]
	);

	public function afterSave($created, $options = array()) {
		Cache::clearGroup('WorkflowsModule', 'workflows_access');
	}

	/**
	 * Get the Access object with pre-loaded access data for current instance $model.
	 * 
	 * @param  string $model Model name.
	 * @return WorkflowAccessObject
	 */
	public function getAccess($model) {
		$class = new WorkflowAccessObject();
		$class->preload($this, $model);

		return $class;
	}

	/**
	 * Get the objects list that ultimately belongs to a certain $model name.
	 * Used for storing each object accesses related to specific section.
	 * 
	 * @param  string $model Model name to search objects by.
	 * @return array         List of Access Objects.
	 */
	public function getObjectListByModel($model) {
		$accessModels = $this->WorkflowAccessModel->getList();

		$objectList = [];
		foreach ($accessModels as $accessModel) {
			$list = ClassRegistry::init('Workflows.' . $accessModel)->findByModelQuery($model);
			$objectList[$accessModel] = array_keys($list);
		}

		return $objectList;
	}

	/*
	 * All manage types that are used in workflows.
	 * @access static
	 */
	 public static function accesses($value = null) {
		$options = array(
			// @todo Stage owner and workflow owner will be the same
			// self::ACCESS_STAGE_OWNER => __('Stage Owner'),

			// stage
			self::ACCESS_OWNER => __('Owner'),
			self::ACCESS_VIEW => __('View Object'),
			self::ACCESS_EDIT => __('Edit Object'),
			self::ACCESS_DELETE => __('Delete Object'),

			// step
			self::ACCESS_CALL => __('Call Stage'),
			self::ACCESS_NOTIFY => __('Notified'),
		);
		return parent::enum($value, $options);
	}
	const ACCESS_OWNER = 1;
	const ACCESS_VIEW = 2;
	const ACCESS_EDIT = 3;
	const ACCESS_DELETE = 4;
	const ACCESS_CALL = 5;
	const ACCESS_NOTIFY = 6;

	/**
	 * Get user IDs that have $access to $accessModel.$accessForeignKey..
	 * 
	 * @param  string      $accessModel      Access model name.
	 * @param  string|int  $accessForeignKey String or integer for access foreign key.
	 * @param  mixed       $access           Null to get all types of accesses, or value for the specific access.
	 *                                       Warning - null value changes format of the returned array.
	 */
	public function parseUsers($accessModel, $accessForeignKey, $access = null) {
		if ($access === null) {
			$ret = [];
			foreach (self::accesses() as $_access => $_accessName) {
				$ret[$_access] = $this->parseUsers($accessModel, $accessForeignKey, $_access);
			}

			return $ret;	
		}

		return call_user_func_array([$this, '_parseUsers'], func_get_args());
	}

	/**
	 * Internal wrapper method that returns array of user IDs.
	 * 
	 * @return array  User IDs
	 */
	protected function _parseUsers($accessModel, $accessForeignKey, $access) {
		$cacheStr = '_parsed_users_'. $accessModel . '_' . $accessForeignKey . '_' . $access;

		if (($users = Cache::read($cacheStr, 'workflows_access')) === false) {
			$data = $this->find('all', [
				'conditions' => [
					'WorkflowAccess.wf_access_model' => $accessModel,
					'WorkflowAccess.wf_access_foreign_key' => $accessForeignKey,
					'WorkflowAccess.access' => $access
				],
				'recursive' => -1
			]);
		
			$users = [];
			foreach ($data as $access) {
				$users = am($users, $this->WorkflowAccessType->processType(
					$access['WorkflowAccess']['wf_access_type'],
					$access['WorkflowAccess']['foreign_key']
				));
			}

			self::uniqueify($users);

			Cache::write($cacheStr, $users, 'workflows_access');
		}

		return $users;
	}

	/**
	 * Make array unique and nicely valued.
	 */
	public static function uniqueify(&$array) {
		if (is_array($array)) {
			$array = array_unique($array);
			$array = array_values($array);
		}
	}

	
}