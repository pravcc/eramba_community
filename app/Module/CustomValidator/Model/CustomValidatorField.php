<?php
App::uses('CustomValidatorAppModel', 'CustomValidator.Model');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class CustomValidatorField extends CustomValidatorAppModel {

    public $actsAs = [
        'FieldData.FieldData'
    ];

/**
 * Validator types.
 */
    const TYPE_VALIDATION = 1;
    const TYPE_OPTIONAL = 2;
    const TYPE_DISABLED = 3;

/**
 * Special values of types.
 */
    const OPTIONAL_VALUE = '__optional__';
    const DISABLED_VALUE = '__disabled__';

/**
 * Special values of types.
 */
    const OPTIONAL_HTML_CLASS = 'optional';
    const DISABLED_HTML_CLASS = 'disabled';
    const MANDATORY_HTML_CLASS = 'mandatory';

    public function __construct($id = false, $table = null, $ds = null) {
        $this->label = __('Custom Vlidator');

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
        ];

        $this->fieldData = [];

        parent::__construct($id, $table, $ds);
    }

/**
 * Default validations.
 * 
 * @return array Default validations.
 */
    public static function getValidations() {
    	return [
			'minCount' => [
				'rule' => ['multiple', ['min' => 1]],
				'message' => __('You have to select at least one option.'),
			],
			'notBlank' => [
				'rule' => ['notBlank'],
                'message' => __('This field cannot be left blank.'),
			],
    	];
    }

/**
 * Return input option for every field data type.
 *
 * @param  string $inputType FieldData input type.
 * @return array Options.
 */
    public static function getValidationOptions($inputType) {
    	$defaultSpecialOptions = [
    		self::OPTIONAL_VALUE => __('Optional'),
    		self::DISABLED_VALUE => __('Not possible'),
    	];

    	$defaultOptions = array_merge($defaultSpecialOptions, [
    		'notBlank' => __('Mandatory'),
    	]);

    	$options = [
    		FieldDataEntity::FIELD_TYPE_SELECT => $defaultOptions,
			FieldDataEntity::FIELD_TYPE_MULTIPLE => array_merge($defaultSpecialOptions, [
	    		'minCount' => __('Mandatory'),
	    	]),
			FieldDataEntity::FIELD_TYPE_DATE => $defaultOptions,
			FieldDataEntity::FIELD_TYPE_TEXT => $defaultOptions,
			FieldDataEntity::FIELD_TYPE_TEXTAREA => $defaultOptions,
			FieldDataEntity::FIELD_TYPE_NUMBER => $defaultOptions,
    	];

    	return $options[$inputType];
    }

    public static function getValidationOptionsFromEntity($FieldData) {
        return self::getValidationOptions($FieldData->config('type'));
    }

/**
 * Get CustomValidatorField.type by options value.
 * 
 * @param  string $value Option value.
 * @return int Type.
 */
    public function getTypeByValue($value) {
    	$typeValueMap = [
    		self::OPTIONAL_VALUE => self::TYPE_OPTIONAL,
    		self::DISABLED_VALUE => self::TYPE_DISABLED,
    	];

    	return (isset($typeValueMap[$value])) ? $typeValueMap[$value] : self::TYPE_VALIDATION;
    }

/**
 * Get CustomValidatorField.validation by options value.
 * 
 * @param  string $value Option value.
 * @return int Validation.
 */
    public function getValidationByValue($value) {
    	$noValidations = [
    		self::OPTIONAL_VALUE,
    		self::DISABLED_VALUE,
    	];

    	return (!in_array($value, $noValidations)) ? $value : null;
    }

/**
 * Get class for HTML elem.
 * 
 * @param  string $value Option value.
 * @return string Class for html elem.
 */
    public static function getHtmlClass($value) {
        $transformMap = [
            self::OPTIONAL_VALUE => self::OPTIONAL_HTML_CLASS,
            self::DISABLED_VALUE => self::DISABLED_HTML_CLASS,
            'minCount' => self::MANDATORY_HTML_CLASS,
            'notBlank' => self::MANDATORY_HTML_CLASS
        ];

        return $transformMap[$value];
    }

/**
 * Save CustomValidatorField instance.
 * 
 * @param  string $model Model name.
 * @param  string $validator Validator name.
 * @param  string $field Field name.
 * @param  string $value Option value.
 * @return boolean Success.
 */
    public function saveCustomValidatorField($model, $validator, $field, $value) {
    	$data = [
    		'model' => $model,
    		'validator' => $validator,
    		'field' => $field,
    	];

    	$itemId = $this->fieldExists($data);

    	if ($itemId) {
    		$data['id'] = $itemId;
    	}

    	$data['type'] = $this->getTypeByValue($value);
    	$data['validation'] = $this->getValidationByValue($value);

    	$this->create();

    	return $this->save($data);
    }

/**
 * Check if field eexists and return id.
 * 
 * @param  array $conditions Conditions.
 * @return int|false Item ID.
 */
    public function fieldExists($conditions) {
    	$data = $this->find('first', [
    		'conditions' => $conditions,
    		'fields' => ['id'],
    		'contain' => [],
    		'recursive' => -1
		]);

		return (!empty($data)) ? $data['CustomValidatorField']['id'] : false;
    }

/**
 * Delete validator fields.
 * 
 * @param  string $model Model name.
 * @param  string $validator Validator name.
 * @param  string $exceptFields Fields that can not be deleted.
 * @return boolean Success.
 */
    public function deleteCustomValidatorFields($model, $validator, $exceptFields = []) {
    	return $this->deleteAll([
    		'model' => $model,
    		'validator' => $validator,
    		'field NOT IN' => $exceptFields
		]);
    }

/**
 * Get custom validator field/fields.
 * 
 * @param  string $model Model name.
 * @param  string $validator Validator name.
 * @param  string $field Field name.
 * @return array CustomValidatorField item.
 */
    public function getCustomValidatorField($model, $validator, $field = null) {
    	$finder = 'all';
    	$conditions = [
			'CustomValidatorField.model' => $model,
			'CustomValidatorField.validator' => $validator,
		];

		if ($field !== null) {
			$finder = 'first';
			$conditions['CustomValidatorField.field'] = $field;
		}

    	return $this->find($finder, [
    		'conditions' => $conditions
		]);
    }

/**
 * Return option value for given field.
 * 
 * @param  string $model Field item.
 * @return string Option value.
 */
    public function getOptionValue($field) {
    	$type = $field['CustomValidatorField']['type'];

    	$typeValueMap = [
    		self::TYPE_OPTIONAL => self::OPTIONAL_VALUE,
    		self::TYPE_DISABLED => self::DISABLED_VALUE,
    	];

    	return (isset($typeValueMap[$type])) ? $typeValueMap[$type] : $field['CustomValidatorField']['validation'];
    }

}
