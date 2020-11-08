<?php
App::uses('BulkActionsAppModel', 'BulkActions.Model');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');

class BulkAction extends BulkActionsAppModel {
	public $hasMany = array(
		'BulkActionObject'
	);

	public $validate = array(
		'model' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false
			)
		),
		'apply_id' => array(
			'multiple' => array(
				'rule' => array('multiple', array('min' => 1)),
				'message' => 'You need to choose one or more objects to perform bulk action',
				'required' => true
			)
		)
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'apply_id' => [
				'label' => __('Apply on'),
				'editable' => true
			],
			'no_change' => [
				// 'type' => 'toggle',
				'label' => __('Leave Unchanged'),
				'editable' => true,
				// 'options' => ['RiskCalculation', 'methods'],
			],
		];

		parent::__construct($id, $table, $ds);
	}

	public function beforeValidate($options = array()) {
		$this->data = $this->processNoChanges($this->data, $this->data['BulkAction']['model']);
		// $Model = ClassRegistry::init($this->data['BulkAction']['model']);
		// if ($Model->hasMethod('parentModel')) {
			// $relatedModel = $Model->parentModel();
			// debug($relatedModel);
			// $this->data = $this->processNoChanges($this->data, $relatedModel);
		// }
		
		if (empty($this->data['BulkAction']['apply_id'])) {
			$this->data['BulkAction']['apply_id'] = array();
		}
		
		$this->addListValidation('type', array_keys(self::actionTypes()));

		// lets build BulkActonObject array for saving each applied object
		$objects = array();
		foreach($this->data['BulkAction']['apply_id'] as $foreignKey) {
			$objects[] = array(
				'model' => $this->data['BulkAction']['model'],
				'foreign_key' => $foreignKey
			);
		}

		$this->data['BulkActionObject'] = $objects;
	}

	public function beforeSave($options = array()) {
		$this->data['BulkAction']['json_data'] = json_encode($this->data);

		// log the user who applied this bulk action
		if ($this->hasMethod('currentUser')) {
			$source = $this->currentUser();
			$this->data['BulkAction']['user_id'] = $source['id'];
		}
	}

	public function processNoChanges($data, $model) {
		if (!empty($data['BulkAction']['no_change'])) {
			foreach ($data['BulkAction']['no_change'] as $field => $toggle) {
				if (!empty($toggle)) {
					unset($data[$model][$field]);
				}
			}
		}

		return $data;
	}

	/**
	 * Get editable fields for bulk submit.
	 */
	public function getEditableEntities(/*$fields, */$model) {
		// $model = ClassRegistry::init($model);
		// $model = _getModelInstance($model);
		$this->bindModel(array(
			'belongsTo' => array(
				$model
			)
		));

		$fieldData = $this->{$model}->getFieldDataEntity();
		$editableEntities = array();
		foreach ($fieldData as $field => $fieldDataEntity) {
			if ($fieldDataEntity->isEditable()) {
				$editableEntities[$field] = $fieldDataEntity;
			}
		}

		return $editableEntities;
	}
	
	/*
	 * static enum: Model::function()
	 * @access static
	 */
	 public static function actionTypes($value = null) {
		$options = array(
			self::TYPE_EDIT => __('Edit'),
			self::TYPE_DELETE => __('Delete')
		);
		return parent::enum($value, $options);
	}
	const TYPE_EDIT = 1;
	const TYPE_DELETE = 2;
}