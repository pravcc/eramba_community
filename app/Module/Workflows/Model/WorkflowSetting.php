<?php
/**
 * @package       Workflows.Model
 */

App::uses('WorkflowsAppModel', 'Workflows.Model');
App::uses('WorkflowAccessType', 'Workflows.Model');
App::uses('WorkflowAccess', 'Workflows.Model');
App::uses('WorkflowStage', 'Workflows.Model');
App::uses('WorkflowStageStep', 'Workflows.Model');
App::uses('AppModule', 'Lib');

class WorkflowSetting extends WorkflowsAppModel {
	public $useTable = 'wf_settings';

	public $actsAs = array(
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'status', 'name', 'description'
			)
		)
	);

	public $belongsTo = [
		'WorkflowInstance' => [
			'className' => 'Workflows.WorkflowInstance',
			'foreignKey' => false,
			// 'conditions' => [
			// 	'WorkflowInstance.model = WorkflowSetting.model'
			// ]
		]
	];

	public $hasMany = array(
		'WorkflowStage' => array(
			'className' => 'Workflows.WorkflowStage',
			'foreignKey' => 'wf_setting_id'
		)
	);

	public $hasAndBelongsToMany = array(
		// worflow owners
		'OwnerUser' => array(
			'className' => 'User',
			'with' => 'Workflows.WorkflowAccess',
			'joinTable' => 'wf_accesses',
			'foreignKey' => 'wf_access_foreign_key',
			'associationForeignKey' => 'foreign_key',
			'conditions' => array(
				'WorkflowAccess.wf_access_model' => 'WorkflowSetting',
				'WorkflowAccess.wf_access_type' => WorkflowAccessType::TYPE_USER,
				'WorkflowAccess.access' => WorkflowAccess::ACCESS_OWNER
			)
		),
		'OwnerGroup' => array(
			'className' => 'Group',
			'with' => 'Workflows.WorkflowAccess',
			'joinTable' => 'wf_accesses',
			'foreignKey' => 'wf_access_foreign_key',
			'associationForeignKey' => 'foreign_key',
			'conditions' => array(
				'WorkflowAccess.wf_access_model' => 'WorkflowSetting',
				'WorkflowAccess.wf_access_type' => WorkflowAccessType::TYPE_GROUP,
				'WorkflowAccess.access' => WorkflowAccess::ACCESS_OWNER
			)
		)
	);

	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			)
		),
		'status' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Status is required'
			),
			'callable' => [
				'rule' => ['callbackValidation', ['WorkflowSetting', 'statuses']],
				'message' => 'Status is not correct'
			],
			'validateStages' => array(
				'rule' => 'validateStages',
				'message' => 'Stages for this section are not completely defined, make sure you have one initial stage and one last stage and the flow from your initial stage to the last stage is continual and intact.' 
			),
		),
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Workflow Settings');

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
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
			'description' => array(
				'label' => __('Description'),
				'editable' => true,
				'description' => __('TBD')
			),
			'OwnerUser' => array(
				'label' => __('Workflow Owner Users'),
				'editable' => true,
				'description' => __('TBD')
			),
			'OwnerGroup' => array(
				'label' => __('Workflow Owner Groups'),
				'editable' => true,
				'description' => __('TBD')
			),
			'status' => array(
				'label' => __('Enabled'),
				'editable' => true,
				'type' => 'toggle',
				'description' => __('Check to enable Workflows for this section')
			)
		);

		parent::__construct($id, $table, $ds);
	}

	public function beforeValidate($options = array()) {
		$ret = true;
		
		if (isset($this->data[$this->alias]['OwnerUser'])) {
			$ret &= $this->validateMultipleFields(['OwnerUser', 'OwnerGroup']);
		}

		return true;
	}

	public function beforeSave($options = array()){
		// $habtmAssoc = [
		// 	'OwnerUser',
		// 	'OwnerGroup'
		// ];

		// // transforms the data array to save the HABTM relation
		// $this->transformDataToHabtm($habtmAssoc);

		// $this->setHabtmConditionsToData($habtmAssoc);

		return true;
	}

	public function afterSave($created, $options = []) {
		if ($this->data[$this->alias]['status'] == self::STATUS_ENABLED) {
			
		}
	}

	/**
	 * Manage instance records to achieve some integrity.
	 */
	public function manageIntegrity($model) {
		$ret = true;

		if ($this->isEnabled($model)) {
			// handles and starts up all object instances on a section
			$ret &= $this->WorkflowInstance->autoCreate($model);
		}

		return $ret;
	}

	/**
	 * Is a workflow enabled on a specified section.
	 */
	public function isEnabled($model) {
		return (bool) $this->find('count', [
			'conditions' => [
				$this->alias . '.model' => $model,
				$this->alias . '.status' => self::STATUS_ENABLED
			],
			'recursive' => -1
		]);
	}

	/**
	 * Method validates if a WorkflowSetting can be enabled for its related section, checking if there is one initial stage and one last stage properly defined.
	 */
	public function validateStages($check) {
		$data = $this->data[$this->alias];

		// for disabled status we dont need this check
		if ($data['status'] == self::STATUS_DISABLED) {
			return true;
		}

		// first check if this request is to enable a workflow for a section and that we have all data needed to do this
		if ($data['status'] == self::STATUS_ENABLED && empty($data[$this->primaryKey])) {
			return false;
		}

		$setting = $this->find('first', array(
			'conditions' => array(
				$this->alias . '.' . $this->primaryKey => $data[$this->primaryKey]
			),
			'fields' => ['model'],
			'recursive' => -1
		));
		$model = $setting['WorkflowSetting']['model'];
		
		// validate count of stage types via WorkflowStage model method
		$valid = $this->WorkflowStage->countStageTypes($model, WorkflowStage::STAGE_INITIAL);
		$valid &= $this->WorkflowStage->countStageTypes($model, WorkflowStage::STAGE_LAST);

		// get the count of stages defined in the current workflow (excluding initial stage)
		// for comparison with the count of default steps
		$compareCountStages = $this->WorkflowStage->countStageTypes($model, [
			WorkflowStage::STAGE_DEFAULT,
			WorkflowStage::STAGE_LAST
		]);

		$stepsQuery = $this->WorkflowStage->WorkflowStageStep->getByModelQuery($model);

		// this condition get the workflow into STRICT mode - each stage would be required to have a default step
		// warning - without this the isntance object class might not work properly
		$stepsQuery['conditions']['WorkflowStageStep.step_type'] = WorkflowStageStep::STEP_TYPE_DEFAULT;
		
		// this makes the workflow required to only have a flow from start to the end, no further restrictions
		$stepsQuery['conditions']['WorkflowStageStep.step_type'] = [
			WorkflowStageStep::STEP_TYPE_DEFAULT,
			WorkflowStageStep::STEP_TYPE_CONDITIONAL
		];

		$stepsQuery['group'] = ['WorkflowStageStep.wf_next_stage_id'];

		// get count of unique default steps for current workflow
		$compareCountSteps = $this->WorkflowStage->WorkflowStageStep->find('count', $stepsQuery);

		// if the counts are equal, it is valid and it means that each stage has 
		$valid &= $comparison = ($compareCountStages == $compareCountSteps);

		return $valid;
	}

	/*
	 * Workflow Setting statuses.
	 * @access static
	 */
	 public static function statuses($value = null) {
		$options = array(
			self::STATUS_DISABLED => __('Disabled'),
			self::STATUS_ENABLED => __('Enabled')
		);
		return parent::enum($value, $options);
	}
	const STATUS_DISABLED = 0;
	const STATUS_ENABLED = 1;

	public function getById($id, $model = null) {
		$data = $this->id = $id;
		if ($m = $this->field('model')) {
			return $this->getItem($m);
		}

		return $this->getItem($model);
	}
	/**
	 * Checks if a workflow setting record exists and auto-creates it if missing, only if the section is whitelisted.
	 * 
	 * @param  Model $Model        Model Instance for which to retrieve the setting record.
	 * @return mixed               Array of data on success, False on failure in case given section is not whitelisted.
	 */
	public function getItem($_model) {
		if ($_model instanceof AppModel) {
			$_model = $_model->alias;
		}

		if (!$this->autoCreate($_model)) {
			trigger_error(__('WorkflowSetting record failed to auto-create and is required to operate workflows.'));
		}

		$data = $this->find('first', array(
			'conditions' => array(
				$this->alias . '.model' => $_model
			),
			'recursive' => 1
		));

		if (empty($data)) {
			throw new NotFoundException();
		}

		return $data;
	}

	/**
	 * Auto-creates a row in the wf_settings table in case is allowed and missing to ease automation.
	 * 
	 * @param  string     $model  Model.
	 * @return bool|array         False on failed save of the setting, array of new saved data on success.
	 */
	public function autoCreate($model) {
		if (!in_array($model, AppModule::instance('Workflows')->whitelist())) {
			throw new ForbiddenException();
		}

		if ($this->itemExists($model)) {
			return true;
		}

		$saveData = [
			'model' => $model,
			'name' => __('Untitled %s Workflow', ClassRegistry::init($model)->label(['singular' => true])),
			'description' => null,
			'status' => self::STATUS_DISABLED
		];

		$this->create();
		$this->set($saveData);

		return $this->save(null, [
			'fieldList' => array_keys($saveData)
		]);
	}

	/**
	 * Check if a setting row exists in database.
	 * 
	 * @param  string  $model Model.
	 * @return bool           True if exists, false otherwise.
	 */
	public function itemExists($model) {
		return (bool)$this->find('count', array(
			'conditions' => array(
				$this->alias . '.model' => $model
			),
			'recursive' => -1
		));
	}

	public function getByModelQuery($model) {
		return [
			'conditions' => [
				'WorkflowSetting.model' => $model
			],
			'joins' => [],
			'recursive' => -1
		];
	}

	/**
	 * Get the array list of [$modelName => $foreignKey] that are descendants (related) for specified WorkflowSetting.
	 * 
	 * @param  string $model Workflow Setting defined by Model name.
	 * @return array         Array list.
	 */
	// public function getAccessList($model) {
	// 	$this->WorkflowAccessModel->find('list', [
	// 		'recursive' => -1
	// 	]);
	// }

	

}