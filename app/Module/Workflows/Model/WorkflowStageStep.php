<?php
/**
 * @package       Workflows.Model
 */

App::uses('WorkflowsAppModel', 'Workflows.Model');
App::uses('WorkflowAccessType', 'Workflows.Model');
App::uses('WorkflowAccess', 'Workflows.Model');

class WorkflowStageStep extends WorkflowsAppModel {
	public $useTable = 'wf_stage_steps';

	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'WorkflowStage' => [
			'className' => 'Workflows.WorkflowStage',
			'foreignKey' => 'wf_stage_id'
		],
		'WorkflowNextStage' => [
			'className' => 'Workflows.WorkflowStage',
			'foreignKey' => 'wf_next_stage_id'
		]
	);

	public $hasMany = array(
		'WorkflowStageStepCondition' => [
			'className' => 'Workflows.WorkflowStageStepCondition',
			'foreignKey' => 'wf_stage_step_id'
		]
	);

	public $hasAndBelongsToMany = array(
		// calls
		'CallUser' => array(
			'className' => 'User',
			'with' => 'Workflows.WorkflowAccess',
			'joinTable' => 'wf_accesses',
			'foreignKey' => 'wf_access_foreign_key',
			'associationForeignKey' => 'foreign_key',
			'conditions' => array(
				'WorkflowAccess.wf_access_model' => 'WorkflowStageStep',
				'WorkflowAccess.wf_access_type' => WorkflowAccessType::TYPE_USER,
				'WorkflowAccess.access' => WorkflowAccess::ACCESS_CALL
			)
		),
		'CallGroup' => array(
			'className' => 'Group',
			'with' => 'Workflows.WorkflowAccess',
			'joinTable' => 'wf_accesses',
			'foreignKey' => 'wf_access_foreign_key',
			'associationForeignKey' => 'foreign_key',
			'conditions' => array(
				'WorkflowAccess.wf_access_model' => 'WorkflowStageStep',
				'WorkflowAccess.wf_access_type' => WorkflowAccessType::TYPE_GROUP,
				'WorkflowAccess.access' => WorkflowAccess::ACCESS_CALL
			)
		),
		
		// notify
		'NotifyUser' => array(
			'className' => 'User',
			'with' => 'Workflows.WorkflowAccess',
			'joinTable' => 'wf_accesses',
			'foreignKey' => 'wf_access_foreign_key',
			'associationForeignKey' => 'foreign_key',
			'conditions' => array(
				'WorkflowAccess.wf_access_model' => 'WorkflowStageStep',
				'WorkflowAccess.wf_access_type' => WorkflowAccessType::TYPE_USER,
				'WorkflowAccess.access' => WorkflowAccess::ACCESS_NOTIFY
			)
		),
		'NotifyGroup' => array(
			'className' => 'Group',
			'with' => 'Workflows.WorkflowAccess',
			'joinTable' => 'wf_accesses',
			'foreignKey' => 'wf_access_foreign_key',
			'associationForeignKey' => 'foreign_key',
			'conditions' => array(
				'WorkflowAccess.wf_access_model' => 'WorkflowStageStep',
				'WorkflowAccess.wf_access_type' => WorkflowAccessType::TYPE_GROUP,
				'WorkflowAccess.access' => WorkflowAccess::ACCESS_NOTIFY
			)
		)
	);

	public $validate = array(
		'wf_stage_id' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'wf_next_stage_id' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'step_type' => array(
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field is required'
			],
			'callable' => [
				'rule' => ['callbackValidation', ['WorkflowStageStep', 'stepTypes']],
				'message' => 'Incorrect step type'
			],
			'validateUniqueStepType' => array(
				'rule' => [
					'validateUniqueStepType',
					'step_type' => [
						WorkflowStageStep::STEP_TYPE_DEFAULT,
						WorkflowStageStep::STEP_TYPE_ROLLBACK
					]
				],
				'message' => 'Step type you selected already exists and there may be only one step with that type created.'
			),
		),
		'notification_message' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'timeout' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field is required'
			],
			'range' => [
				'rule' => ['range', 0, 301],
				'message' => 'Please provide a number between 1 - 300'
			]
		]
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Workflow Next Stages');

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
			'notifications' => array(
				'label' => __('Notifications')
			),
			// 'triggers' => array(
			// 	'label' => __('Triggers')
			// ),
			'calls' => array(
				'label' => __('Calls')
			)
		);

		$this->fieldData = array(
			'step_type' => array(
				'label' => __('Step Type'),
				'editable' => true,
				'options' => ['WorkflowStageStep', 'stepTypes'],
				'description' => __('Choose what type of association step are you creating. Bare in mind there can be only one "Default" step.'),
				'empty' => __('Choose a type of a connection ...')
			),
			'wf_next_stage_id' => array(
				'label' => __('Connect to Stage'),
				'editable' => true,
				'options' => [
					'callable' => [$this, 'getNextStageOptions'],
					'passParams' => true
				],
				'description' => __('Choose a Workflow Stage you want to connect to the current stage.'),
				'empty' => __('Choose a Stage ...')
			),
			'notification_message' => array(
				'label' => __('Notification Message'),
				'editable' => true,
				'group' => 'notifications',
				'description' => __('TBD notificaion message')
			),
			'timeout' => array(
				'label' => __('Timeout (hours)'),
				'editable' => true,
				'description' => __('Choose when this stage expires in hours')
			),
			'NotifyUser' => array(
				'label' => __('Users'),
				'editable' => true,
				'group' => 'notifications',
				'description' => __('What users are notified')
			),
			'NotifyGroup' => array(
				'label' => __('Groups'),
				'editable' => true,
				'group' => 'notifications',
				'description' => __('Groups that will be notified')
			),
			// 'ApprovalCustom' => array(
			// 	'label' => __('Custom Roles'),
			// 	'editable' => true,
			// 	'group' => 'approvals',
			// 	'options' => ['tbd', 'tbd2'],
			// 	'description' => __('Who needs to give the approve to enter this stage')
			// ),
			// 'TriggerUser' => array(
			// 	'label' => __('Users'),
			// 	'editable' => true,
			// 	'group' => 'triggers',
			// 	'description' => __('Users that can trigger this stage')
			// ),
			// 'TriggerGroup' => array(
			// 	'label' => __('Groups'),
			// 	'editable' => true,
			// 	'group' => 'triggers',
			// 	'description' => __('Groups that can trigger this stage')
			// ),
			'CallUser' => array(
				'label' => __('Users'),
				'editable' => true,
				'group' => 'calls',
				'description' => __('Users that can call this stage')
			),
			'CallGroup' => array(
				'label' => __('Groups'),
				'editable' => true,
				'group' => 'calls',
				'description' => __('Groups that can call this stage')
			)
		);

		parent::__construct($id, $table, $ds);
	}

	public function beforeValidate($options = array()) {
		$ret = true;

		$type = $this->data[$this->alias]['step_type'];

		// conditional step requires to pass validation and have at least one condition configured
		if (self::isTypeConditional($type)) {
			if (!isset($this->data['WorkflowStageStepCondition'])) {
				$this->data['WorkflowStageStepCondition'] = [];
			}

			if (empty($this->data['WorkflowStageStepCondition'])) {
				$this->invalidate('step_conditions' , __('You have to configure at least one condition that triggers the next step.'));
			}
		}
		
		// by default there needs to be at least someone that is notified
		$ret &= $this->validateMultipleFields(['NotifyUser', 'NotifyGroup']);

		// only default steps can be called by someone, the rest are automatic
		if (self::isTypeCallable($type)) {
			$ret &= $this->validateMultipleFields(['CallUser', 'CallGroup']);
		}

		if ($type != self::STEP_TYPE_ROLLBACK) {
			$this->validator()->remove('timeout');
		}

		return true;
	}

	public function beforeSave($options = array()){
		$ret = true;
		if (!empty($this->data[$this->alias][$this->primaryKey])) {
			$ret &= $this->WorkflowStageStepCondition->deleteAll([
				'WorkflowStageStepCondition.wf_stage_step_id' => $this->data[$this->alias][$this->primaryKey]
			]);
		}

		// $habtmAssoc = [
		// 	'NotifyUser',
		// 	'NotifyGroup',
		// 	// 'TriggerUser',
		// 	// 'TriggerGroup',
		// 	'CallUser',
		// 	'CallGroup'
		// ];

		// // transforms the data array to save the HABTM relation
		// $this->transformDataToHabtm($habtmAssoc);

		// $this->setHabtmConditionsToData($habtmAssoc);
		
		return $ret;
	}

	public function validateUniqueStepType($check, $uniqueTypes) {
		$value = array_values($check);
		$stepType = $value[0];
		
		// we continue the validation check only if its the step type needed to be checked (initial, last)
		if (!in_array($stepType, $uniqueTypes)) {
			return true;
		}

		// lets find out $stageId value
		$stageId = $this->_retrieveStageId($this->data);

		$conds = [];
		// custom conditions to apply on the counting method, to skip record currently being saved
		if (isset($this->data[$this->alias][$this->primaryKey])) {
			$conds = [
				sprintf('%s.%s !=', $this->alias, $this->primaryKey) => $this->data[$this->alias][$this->primaryKey]
			];
		}
		$count = $this->countStepTypes($stageId, $stepType, $conds);

		// return true only if count is 0, otherwise false to fail validation
		return $count == 0;
	}

	protected function _retrieveStageId(array $data) {
		if (isset($data[$this->alias]['wf_stage_id'])) {
			return $data[$this->alias]['wf_stage_id'];
		}
		
		if (isset($data[$this->alias][$this->primaryKey])) {
			$id = $data[$this->alias][$this->primaryKey];

			$data = $this->getItem($id);
			return $this->_retrieveStageId($data);
		}

		return false;
	}

	/**
	 * Options for next stages select box, queried by model.
	 */
	public function getNextStageOptions(FieldDataEntity $Field, $model) {
		$query = $Field->buildRelatedQuery();
		$query['conditions']['WorkflowNextStage.model'] = $model;
		
		return $Field->findRelated($query);
	}

	/**
	 * Builds a join query that travers down to the WorkflowSetting model.
	 */
	public function getByModelQuery($model) {
		$queryFragment = [
			'joins' => [
				2 => [
					'table' => 'wf_stages',
					'alias' => 'WorkflowStage',
					'type' => 'INNER',
					'conditions' => [
						'WorkflowStage.id = WorkflowStageStep.wf_stage_id'
					]
				],
			]
		];

		$modelQuery = $this->WorkflowStage->getByModelQuery($model);
		$query = Hash::merge($modelQuery, $queryFragment);

		return $query;
	}

	/**
	 * Get a count of step connections with specified step type. Used for validation.
	 * 
	 * @param  string $stageId     Stage ID.
	 * @param  int    $stepType   Stage Type.
	 * @param  array  $customConds Custom conditions to apply.
	 * @return int                 Count.
	 */
	public function countStepTypes($stageId, $stepType, array $customConds = []) {
		$conds = [
			$this->alias . '.wf_stage_id' => $stageId,
			$this->alias . '.step_type' => $stepType
		];

		if (!empty($customConds)) {
			$conds = am($conds, $customConds);
		}

		return $this->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);
	}

	/**
	 * Returns data array with everything about next stages from the specified stage.
	 * @param  int $stageId  Stage ID.
	 * @return array         Data about next stages.
	 */
	public function getNextStages($stageId) {
		$data = $this->find('all', [
			'conditions' => [
				'WorkflowStageStep.wf_stage_id' => $stageId
			],
			'contain' => [
				'WorkflowNextStage',
				'WorkflowStageStepCondition'
			],
			'order' => ['WorkflowStageStep.step_type' => 'ASC']
		]);

		return $data;
	}

	public function getItem($id) {
		return $this->find('first', [
			'conditions' => [
				$this->alias . '.id' => $id
			],
			'recursive' => -1
		]);
	}

	/**
	 * Get the next default step by $stageId.
	 * 
	 * @param  int   $stageId    Next Stage ID.
	 * @return array 	         Stage Step data array.
	 */
	public function getNextDefaultStep($stageId) {
		return $this->find('first', [
			'conditions' => [
				$this->alias . '.wf_next_stage_id' => $stageId,
				$this->alias . '.step_type' => self::STEP_TYPE_DEFAULT
			],
			'recursive' => -1
		]);
	}

	/**
	 * Check if a provided step type can be called by some user - conditional ones are not default.
	 * 
	 * @param  string|int  $type Step type.
	 * @return boolean           True if step can be called, False otherwise.
	 */
	public static function isTypeCallable($type) {
		return $type == self::STEP_TYPE_DEFAULT;
	}

	public static function isTypeConditional($type) {
		return $type == self::STEP_TYPE_CONDITIONAL;
	}
	
	/*
	 * Workflow stage steps.
	 * @access static
	 */
	 public static function stepTypes($value = null) {
		$options = array(
			self::STEP_TYPE_DEFAULT => __('Default'),
			self::STEP_TYPE_CONDITIONAL => __('Conditional'),
			self::STEP_TYPE_ROLLBACK => __('Rollback')
		);
		return parent::enum($value, $options);
	}
	const STEP_TYPE_DEFAULT = 1;
	const STEP_TYPE_CONDITIONAL = 2;
	const STEP_TYPE_ROLLBACK = 3;

}