<?php
/**
 * @package       Workflows.Model
 */

App::uses('WorkflowsAppModel', 'Workflows.Model');
App::uses('WorkflowAccessType', 'Workflows.Model');
App::uses('WorkflowAccess', 'Workflows.Model');
App::uses('WorkflowStageStep', 'Workflows.Model');
App::uses('Wf_Stage', 'Workflows.Lib');

class WorkflowStage extends WorkflowsAppModel {
	public $useTable = 'wf_stages';

	public $actsAs = array(
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',	
			'fields' => array(
				'name'
			)
		)
	);

	public $belongsTo = [
		'WorkflowSetting' => [
			'className' => 'Workflows.WorkflowSetting',
			'foreignKey' => 'wf_setting_id'
		]
	];

	public $hasMany = array(
		'ApprovalCustom' => array(
			'className' => 'Workflows.WorkflowAccess',
			'foreignKey' => 'wf_access_foreign_key',
			'conditions' => array(
				'ApprovalCustom.wf_access_model' => 'WorkflowStage',
				'ApprovalCustom.wf_access_type' => WorkflowAccessType::TYPE_CUSTOM,
				'ApprovalCustom.access' => WorkflowAccess::ACCESS_OWNER
			)
		),
		'WorkflowStageStep' => [
			'className' => 'Workflows.WorkflowStageStep',
			'foreignKey' => 'wf_stage_id',
		]
	);

	public $hasOne = array(
		'DefaultStep' => [
			'className' => 'Workflows.WorkflowStageStep',
			'foreignKey' => 'wf_stage_id',
			'conditions' => [
				'DefaultStep.step_type' => WorkflowStageStep::STEP_TYPE_DEFAULT
			]
		],
		'RollbackStep' => [
			'className' => 'Workflows.WorkflowStageStep',
			'foreignKey' => 'wf_stage_id',
			'conditions' => [
				'RollbackStep.step_type' => WorkflowStageStep::STEP_TYPE_ROLLBACK
			]
		]
	);

	public $hasAndBelongsToMany = array(
		// next stages for a stage
		'NextStage' => array(
			'className' => 'Workflows.WorkflowStage',
			'with' => 'Workflows.WorkflowStageStep',
			'joinTable' => 'wf_stage_steps',
			'foreignKey' => 'wf_stage_id',
			'associationForeignKey' => 'wf_next_stage_id'
		),
		'NextStage' => array(
			'className' => 'Workflows.WorkflowStage',
			'with' => 'Workflows.WorkflowStageStep',
			'joinTable' => 'wf_stage_steps',
			'foreignKey' => 'wf_stage_id',
			'associationForeignKey' => 'wf_next_stage_id'
		),

		// approvals
		'ApprovalUser' => array(
			'className' => 'User',
			'with' => 'Workflows.WorkflowAccess',
			'joinTable' => 'wf_accesses',
			'foreignKey' => 'wf_access_foreign_key',
			'associationForeignKey' => 'foreign_key',
			'conditions' => array(
				'WorkflowAccess.wf_access_model' => 'WorkflowStage',
				'WorkflowAccess.wf_access_type' => WorkflowAccessType::TYPE_USER,
				'WorkflowAccess.access' => WorkflowAccess::ACCESS_OWNER
			)
		),
		'ApprovalGroup' => array(
			'className' => 'Group',
			'with' => 'Workflows.WorkflowAccess',
			'joinTable' => 'wf_accesses',
			'foreignKey' => 'wf_access_foreign_key',
			'associationForeignKey' => 'foreign_key',
			'conditions' => array(
				'WorkflowAccess.wf_access_model' => 'WorkflowStage',
				'WorkflowAccess.wf_access_type' => WorkflowAccessType::TYPE_GROUP,
				'WorkflowAccess.access' => WorkflowAccess::ACCESS_OWNER
			)
		),

		// management view
		'ManageViewUser' => array(
			'className' => 'User',
			'with' => 'Workflows.WorkflowAccess',
			'joinTable' => 'wf_accesses',
			'foreignKey' => 'wf_access_foreign_key',
			'associationForeignKey' => 'foreign_key',
			'conditions' => array(
				'WorkflowAccess.wf_access_model' => 'WorkflowStage',
				'WorkflowAccess.wf_access_type' => WorkflowAccessType::TYPE_USER,
				'WorkflowAccess.access' => WorkflowAccess::ACCESS_VIEW
			)
		),
		'ManageViewGroup' => array(
			'className' => 'Group',
			'with' => 'Workflows.WorkflowAccess',
			'joinTable' => 'wf_accesses',
			'foreignKey' => 'wf_access_foreign_key',
			'associationForeignKey' => 'foreign_key',
			'conditions' => array(
				'WorkflowAccess.wf_access_model' => 'WorkflowStage',
				'WorkflowAccess.wf_access_type' => WorkflowAccessType::TYPE_GROUP,
				'WorkflowAccess.access' => WorkflowAccess::ACCESS_VIEW
			)
		),

		// management edit
		'ManageEditUser' => array(
			'className' => 'User',
			'with' => 'Workflows.WorkflowAccess',
			'joinTable' => 'wf_accesses',
			'foreignKey' => 'wf_access_foreign_key',
			'associationForeignKey' => 'foreign_key',
			'conditions' => array(
				'WorkflowAccess.wf_access_model' => 'WorkflowStage',
				'WorkflowAccess.wf_access_type' => WorkflowAccessType::TYPE_USER,
				'WorkflowAccess.access' => WorkflowAccess::ACCESS_EDIT
			)
		),
		'ManageEditGroup' => array(
			'className' => 'Group',
			'with' => 'Workflows.WorkflowAccess',
			'joinTable' => 'wf_accesses',
			'foreignKey' => 'wf_access_foreign_key',
			'associationForeignKey' => 'foreign_key',
			'conditions' => array(
				'WorkflowAccess.wf_access_model' => 'WorkflowStage',
				'WorkflowAccess.wf_access_type' => WorkflowAccessType::TYPE_GROUP,
				'WorkflowAccess.access' => WorkflowAccess::ACCESS_EDIT
			)
		),

		// management delete
		'ManageDeleteUser' => array(
			'className' => 'User',
			'with' => 'Workflows.WorkflowAccess',
			'joinTable' => 'wf_accesses',
			'foreignKey' => 'wf_access_foreign_key',
			'associationForeignKey' => 'foreign_key',
			'conditions' => array(
				'WorkflowAccess.wf_access_model' => 'WorkflowStage',
				'WorkflowAccess.wf_access_type' => WorkflowAccessType::TYPE_USER,
				'WorkflowAccess.access' => WorkflowAccess::ACCESS_DELETE
			)
		),
		'ManageDeleteGroup' => array(
			'className' => 'Group',
			'with' => 'Workflows.WorkflowAccess',
			'joinTable' => 'wf_accesses',
			'foreignKey' => 'wf_access_foreign_key',
			'associationForeignKey' => 'foreign_key',
			'conditions' => array(
				'WorkflowAccess.wf_access_model' => 'WorkflowStage',
				'WorkflowAccess.wf_access_type' => WorkflowAccessType::TYPE_GROUP,
				'WorkflowAccess.access' => WorkflowAccess::ACCESS_DELETE
			)
		)
	);

	public $validate = array(
		'model' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'name' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			)
		),
		'stage_type' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			),
			'callable' => [
				'rule' => ['callbackValidation', ['WorkflowStage', 'stageTypes']],
				'message' => 'Incorrect stage type'
			],
			'validateUniqueStageType' => array(
				'rule' => [
					'validateUniqueStageType',
					[
						WorkflowStage::STAGE_INITIAL,
						WorkflowStage::STAGE_LAST
					]
				],
				'message' => 'Stage type you selected already exists and there may be only one Stage with that type created.'
			),
		),
		'approval_method' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			)
		),
		'ApprovalUser' => array(
			'multiple' => array(
				'rule' => array('multiple', array('min' => 1)),
				'required' => true,
				'message' => 'Please select at least one user'
			)
		),
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Workflow Stages');

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
			'approvals' => array(
				'label' => __('Approvals')
			),
			'manage-view' => array(
				'label' => __('Allow View')
			),
			'manage-edit' => array(
				'label' => __('Allow Modifications')
			),
			'manage-delete' => array(
				'label' => __('Allow Deletions')
			)
		);

		$this->fieldData = array(
			'model' => array(
				'label' => __('Section'),
				'editable' => false
			),
			'name' => array(
				'label' => __('Name'),
				'editable' => true,
				'description' => __('Name your Workflow Stage')
			),
			'test' => array(
				'label' => __('test'),
				'editable' => true,
				'type' => 'toggle',
				'description' => __('sssds your Workflow Stage')
			),
			'description' => array(
				'label' => __('Description'),
				'editable' => true,
				'description' => __('TBD')
			),
			'stage_type' => array(
				'label' => __('Stage Type'),
				'options' => array('WorkflowStage', 'stageTypes'),
				'editable' => true,
				'description' => __('Workflow Stage type'),
				'empty' => __('Choose a type ...')
			),
			'ApprovalUser' => array(
				'label' => __('Users'),
				'editable' => true,
				'group' => 'approvals',
				'description' => __('Who needs to give the approve to enter this stage')
			),
			'ApprovalGroup' => array(
				'label' => __('Groups'),
				'editable' => true,
				'group' => 'approvals',
				'description' => __('Who needs to give the approve to enter this stage')
			),
			'ApprovalCustom' => array(
				'label' => __('Custom Roles'),
				'editable' => true,
				'group' => 'approvals',
				'options' => ['tbd', 'tbd2'],
				'description' => __('Who needs to give the approve to enter this stage')
			),
			'approval_method' => array(
				'label' => __('Approval Requirements'),
				'options' => array('WorkflowStage', 'approvalMethods'),
				'editable' => true,
				'group' => 'approvals',
				'empty' => __('Approval is needed from ...'),
				'description' => __('Choose which of the selected approval objects are needed')
			),
			'ManageViewUser' => array(
				'label' => __('Users'),
				'editable' => true,
				'group' => 'manage-view',
				'description' => __('Allow view.')
			),
			'ManageViewGroup' => array(
				'label' => __('Groups'),
				'editable' => true,
				'group' => 'manage-view',
				'description' => __('Allow view.')
			),
			'ManageEditUser' => array(
				'label' => __('Users'),
				'editable' => true,
				'group' => 'manage-edit',
				'description' => __('Allow edits.')
			),
			'ManageEditGroup' => array(
				'label' => __('Groups'),
				'editable' => true,
				'group' => 'manage-edit',
				'description' => __('Allow edits.')
			),
			'ManageDeleteUser' => array(
				'label' => __('Users'),
				'editable' => true,
				'group' => 'manage-delete',
				'description' => __('Allow deletion.')
			),
			'ManageDeleteGroup' => array(
				'label' => __('Groups'),
				'editable' => true,
				'group' => 'manage-delete',
				'description' => __('Allow deletion.')
			),
		);

		parent::__construct($id, $table, $ds);

		$this->order = [$this->alias . '.stage_type' => 'ASC'];
	}

	public function beforeSave($options = array()){
		// in case of a new stage being added we set a wf_setting_id for association
		if (isset($this->data[$this->alias]['model']) && empty($this->data[$this->alias][$this->primaryKey])) {
			$setting = $this->WorkflowSetting->getItem($this->data[$this->alias]['model']);
			$this->data[$this->alias]['wf_setting_id'] = $setting['WorkflowSetting']['id'];
		}

		// $habtmAssoc = [
		// 	'ApprovalUser',
		// 	'ApprovalGroup',
		// 	'ManageViewUser',
		// 	'ManageViewGroup',
		// 	'ManageEditUser',
		// 	'ManageEditGroup',
		// 	'ManageDeleteUser',
		// 	'ManageDeleteGroup'
		// ];

		// // transforms the data array to save the HABTM relation
		// $this->transformDataToHabtm($habtmAssoc);

		// $this->setHabtmConditionsToData($habtmAssoc);

		return true;
	}

	public function validateUniqueStageType($check, $uniqueTypes) {
		$value = array_values($check);
		$stageType = $value[0];
		
		// we continue the validation check only if its the stage type needed to be checked (initial, last)
		if (!in_array($stageType, $uniqueTypes)) {
			return true;
		}

		// lets find out $model value
		$model = $this->_retrieveModelName($this->data);

		$conds = [];
		// custom conditions to apply on the counting method, to skip record currently being saved
		if (isset($this->data[$this->alias][$this->primaryKey])) {
			$conds = [
				sprintf('%s.%s !=', $this->alias, $this->primaryKey) => $this->data[$this->alias][$this->primaryKey]
			];
		}
		$count = $this->countStageTypes($model, $stageType, $conds);

		// return true only if count is 0, otherwise false to fail validation
		return $count == 0;
	}

	/**
	 * Parses model name from an array of data.
	 */
	protected function _retrieveModelName(array $data) {
		if (isset($data[$this->alias]['model'])) {
			return $data[$this->alias]['model'];
		}
		
		if (isset($data[$this->alias][$this->primaryKey])) {
			$id = $data[$this->alias][$this->primaryKey];

			$data = $this->getItem($id);
			return $this->_retrieveModelName($data);
		}

		return false;
	}

	/**
	 * Get a Stage item.
	 * 
	 * @param  int $id  ID.
	 */
	public function getItem($id) {
		$data = $this->find('first', array(
			'conditions' => array(
				$this->alias . '.' . $this->primaryKey => $id
			),
			'recursive' => -1
		));

		if (empty($data)) {
			throw new NotFoundException();
		}

		return $data;
	}

	/**
	 * Get a count of stages with specified stage type. Used for validation.
	 * 
	 * @param  string $model       Model name.
	 * @param  int    $stageType   Stage Type.
	 * @param  array  $customConds Custom conditions to apply.
	 * @return int                 Count.
	 */
	public function countStageTypes($model, $stageType, array $customConds = []) {
		$conds = [
			'WorkflowStage.model' => $model,
			'WorkflowStage.stage_type' => $stageType
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
	 * Returns the Initial stage for defined section.
	 * 
	 * @param  string $model Model name.
	 * @return array         Initial stage.
	 */
	public function getInitialStage($model) {
		return $this->find('first', [
			'conditions' => [
				'WorkflowStage.model' => $model,
				'WorkflowStage.stage_type' => WorkflowStage::STAGE_INITIAL
			],
			'recursive' => -1
		]);
	}

	/**
	 * Builds a join query that travers down to the WorkflowSetting model.
	 */
	public function getByModelQuery($model) {
		$queryFragment = [
			'joins' => [
				3 => [
					'table' => 'wf_settings',
					'alias' => 'WorkflowSetting',
					'type' => 'INNER',
					'conditions' => [
						'WorkflowSetting.id = WorkflowStage.wf_setting_id'
					]
				],
			]
		];

		$modelQuery = $this->WorkflowSetting->getByModelQuery($model);
		$query = Hash::merge($modelQuery, $queryFragment);

		return $query;
	}

	/**
	 * Finds stages by a model name.
	 */
	public function findByModel($model) {
		$query = $this->getByModelQuery($model);
		$query['fields'] = [
			'WorkflowStage.id',
			'WorkflowStage.name'
		];

		return $this->find('list', $query);
	}

	/*
	 * Workflow stage types.
	 * @access static
	 */
	 public static function stageTypes($value = null) {
		$options = array(
			self::STAGE_INITIAL => __('Initial'),
			self::STAGE_DEFAULT => __('Default'),
			self::STAGE_LAST => __('Last')
		);
		return parent::enum($value, $options);
	}
	const STAGE_INITIAL = 1;
	const STAGE_DEFAULT = 2;
	const STAGE_LAST = 3;

	/*
	 * Required approvals for this stage is needed by one or all of them.
	 * @access static
	 */
	 public static function approvalMethods($value = null) {
		$options = array(
			self::METHOD_SINGLE => __('At least one'),
			self::METHOD_ALL => __('All of them')
		);
		return parent::enum($value, $options);
	}
	const METHOD_SINGLE = 1;
	const METHOD_ALL = 2;

}