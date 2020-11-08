<?php
/**
 * @package       Workflows.Model
 */

App::uses('WorkflowsAppModel', 'Workflows.Model');
App::uses('Hash', 'Utility');

class WorkflowStageStepCondition extends WorkflowsAppModel {
	public $useTable = 'wf_stage_step_conditions';

	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'WorkflowStageStep' => [
			'className' => 'Workflows.WorkflowStageStep',
			'foreignKey' => 'wf_stage_step_id'
		]
	);

	public $validate = array(
		// 'wf_stage_step_id' => array(
		// 	'rule' => 'notBlank',
		// 	'required' => true,
		// 	'allowEmpty' => false
		// ),
		'field' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		],
		'comparison_type' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		],
		'value' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		],
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Workflow Next Stages');

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			)
		);

		$this->fieldData = array(
			'field' => array(
				'label' => __('Field'),
				'editable' => true,
				'options' => [
					'callable' => [$this, 'getFieldOptions'],
					'passParams' => true
				],
				'description' => __('Choose a field.'),
				'empty' => __('Choose one ...')
			),
			'comparison_type' => array(
				'label' => __('Comparison Type'),
				'editable' => true,
				'options' => ['WorkflowStageStepCondition', 'comparisonTypes'],
				'description' => __('Choose comparison type.'),
				'empty' => __('Choose one ...')
			),
			'value' => array(
				'label' => __('Value'),
				'editable' => true,
				// 'empty' => __('Choose one ...')
			),
		);

		parent::__construct($id, $table, $ds);
	}

	/**
	 * Get options for conditional fields.
	 */
	public function getFieldOptions(FieldDataEntity $Field, $model) {
		$Model = ClassRegistry::init($model);
		$fieldList = $Model->Behaviors->Triggerable->settings[$Model->alias]['fieldList'];

		$FieldCollection = $Model->getFieldDataEntity();
		$values = $FieldCollection->getList($fieldList);
		
		return $values;
	}

	/**
	 * Builds a join query that travers down to the WorkflowSetting model.
	 */
	public function getByModelQuery($model) {
		$queryFragment = [
			'joins' => [
				1 =>[
					'table' => 'wf_stage_steps',
					'alias' => 'WorkflowStageStep',
					'type' => 'INNER',
					'conditions' => [
						'WorkflowStageStep.id = WorkflowStageStepCondition.wf_stage_step_id'
					]
				]
			]
		];

		$modelQuery = $this->WorkflowStageStep->getByModelQuery($model);
		$query = Hash::merge($modelQuery, $queryFragment);

		return $query;
	}

	/**
	 * Get all conditions defined on fields that belongs to a certain model name.
	 * 
	 * @param  string $model Model name.
	 * @return array         Conditions data array.
	 */
	public function findByModel($model) {
		$query = $this->getByModelQuery($model);
		$query['fields'] = [
			'WorkflowStageStepCondition.*',
			'WorkflowStageStep.wf_next_stage_id'
		];
		
		$data = $this->find('all', $query);
		
		return $data;
	}

	/*
	 * Types of objects that give approvals.
	 * @access static
	 */
	 public static function comparisonTypes($value = null) {
		$options = array(
			self::COMPARE_EQUAL => '=',
			self::COMPARE_LT => '<',
			self::COMPARE_HT => '>'
		);
		return parent::enum($value, $options);
	}
	const COMPARE_EQUAL = 1;
	const COMPARE_LT = 2;
	const COMPARE_HT = 3;

	/**
	 * Compare 2 values using comparison type constant variable.
	 */
	public static function compare($value1, $value2, $comparisonType) {
		$strcmp = strcmp($value1, $value2);
		$map = self::mapStrcmp($strcmp);

		return $map == $comparisonType;
	}

	/**
	 * Map a strcmp function result to compare constants of this class.
	 */
	public static function mapStrcmp($strcmp) {
		if ($strcmp == 0) {
			return self::COMPARE_EQUAL;
		}

		if ($strcmp < 0) {
			return self::COMPARE_LT;
		}

		if ($strcmp > 0) {
			return self::COMPARE_HT;
		}
	}

}