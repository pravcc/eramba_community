<?php
/**
 * Copyright 2009-2014, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2009-2014, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('ModelBehavior', 'Model');
App::uses('WorkflowStageStepCondition', 'Workflows.Model');

/**
 * Triggerable Behavior
 */
class TriggerableBehavior extends ModelBehavior {

/**
 * Settings array
 *
 * @var array
 */
	public $settings = array();

/**
 * Default settings
 *
 * fieldList                  - the field list to check during a save
 *
 * @var array
 */
	protected $_defaults = array(
		'fieldList' => null
	);

/**
 * Setup
 *
 * @param Model $model Model instance that behavior is attached to
 * @param array $config Configuration settings from model
 * @return void
 */
	public function setup(Model $Model, $config = array()) {
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = $this->_defaults;
		}

		$this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], $config);
		$this->Condition = ClassRegistry::init('Workflows.WorkflowStageStepCondition');
		$this->Instance = ClassRegistry::init('Workflows.WorkflowInstance');

		$this->settings[$Model->alias]['enabled'] = $this->Instance->WorkflowSetting->isEnabled($Model->alias);

		$fields = $this->Condition->findByModel($Model->alias);
		$this->settings[$Model->alias]['_preparedFields'] = $this->_prepareFields($fields);

		// this works only with AuditableBehavior enabled
		if ($Model->Behaviors->enabled('Auditable') !== true) {
			trigger_error(__('Workflows TriggerableBehavior works only when AuditableBehavior is enabled.'));
		}
	}

	public function cleanup(Model $Model) {
		$this->Condition = null;
		unset($this->Condition);
		$this->Instance = null;
		unset($this->Instance);

		parent::cleanup($Model);
	}

	public function afterSave(Model $Model, $created, $options = array()) {
		if ($this->settings[$Model->alias]['enabled'] !== true) {
			return true;
		}

		if ($created) {
			return $this->Instance->autoCreate($Model->alias, $Model->id);
		}
		return true;
	}

	/**
	 * When a property triggers and is needed for workflows auto-triggering conditional feature, we compare condition
	 * and trigger related stage if needed.
	 */
	public function afterAuditProperty($Model, $propertyName, $oldValue, $newValue) {
		if ($this->settings[$Model->alias]['enabled'] !== true) {
			return true;
		}

		if ($this->hasField($Model, $propertyName) && $oldValue != $newValue) {
			// $Field = $this->getField($Model, $propertyName);
			$condition = $this->settings[$Model->alias]['_preparedFields'][$propertyName];

			$resultOld = WorkflowStageStepCondition::compare(
				$oldValue,
				$condition['value'],
				$condition['comparison_type']
			);

			$result = WorkflowStageStepCondition::compare(
				$newValue,
				$condition['value'],
				$condition['comparison_type']
			);

			// triggered condition only if previous $resultOld value comparison was not triggered already
			if (!$resultOld && $result) {
				$Instance = ClassRegistry::init('Workflows.WorkflowInstance');
				$list = $Instance->getNoRequestInstances($Model->alias);

				$ret = true;
				if (!empty($list)) {
					foreach ($list as $id) {
						// call a stage on each one of the triggered instances that doesnt have any pending request
						$ret &= $Instance->call_stage(
							$id,
							$condition['wf_next_stage_id'],
							$condition['wf_stage_step_id']
						);
					}
				}

				if (!$ret) {
					trigger_error(
						__('Error occured while trying to trigger a workflow conditional stage automatically on $s section and one or more Instance IDs: %s.', $Model->alias, implode(', ', $list))
					);
				}
			}
		}
	}

	/**
	 * Prepare fields array for better accessibility.
	 */
	protected function _prepareFields($fields) {
		$data = [];
		foreach ($fields as $field) {
			$compare = ($field['WorkflowStageStepCondition']['comparison_type']);
			$_field = $field['WorkflowStageStepCondition']['field'];

			$data[$_field] = [
				'comparison_type' => $compare,
				'value' => $field['WorkflowStageStepCondition']['value'],
				'wf_stage_step_id' => $field['WorkflowStageStepCondition']['wf_stage_step_id'],
				'wf_next_stage_id' => $field['WorkflowStageStep']['wf_next_stage_id']
			];
		}

		return $data;
	}

	protected function getField(Model $Model, $propertyName) {
		if ($this->hasField($Model, $propertyName)) {
			return $Model->getFieldDataEntity($propertyName);
		}
		
		return false;
	}

	/**
	 * Checks if property being modified is one of the field list values that should be handled by workflows as well.
	 */
	protected function hasField(Model $Model, $propertyName) {
		$hasField = in_array($propertyName, $this->settings[$Model->alias]['fieldList']);
		$hasField &= $Model->hasFieldDataEntity($propertyName);

		return $hasField;
	}

}