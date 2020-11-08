<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');
App::uses('CustomValidatorField', 'CustomValidator.Model');

class CustomValidatorBehavior extends ModelBehavior {

/**
 * Default config
 *
 * @var array
 */
    protected $_defaults = [
    ];

    public $settings = [];

/**
 * Setup
 *
 * @param Model $Model
 * @param array $settings
 * @throws RuntimeException
 * @return void
 */
    public function setup(Model $Model, $settings = []) {
        if (!isset($this->settings[$Model->alias])) {
            $this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
        }

        $this->_loadSettings($Model);
    }

/**
 * Load custom validator settings from model.
 * 
 * @param Model $Model
 * @return void
 */
    protected function _loadSettings(Model $Model) {
        if (!$Model->hasMethod('getCustomValidatorConfig')) {
            return trigger_error('CustomValidator: Model %s is missing custom validator configuration when loading it up.', $Model->alias);
        }

        $config = $Model->getCustomValidatorConfig();
        $this->settings[$Model->alias]['validators'] = $this->_normalizeSettings($Model, $config);
    }

/**
 * Normalized formating of config.
 *
 * @param Model $Model
 * @param array $config
 * @return array
 */
    protected function _normalizeSettings(Model $Model, $config) {
        $defaultConfig = [
            'title' => '',
            'conditions' => [],
            'fields' => [],
        ];

        $config = Hash::normalize($config);

        foreach ($config as $key => $settings) {
            $normalConfig[$key] = $settings;
            $normalConfig[$key] = Hash::normalize($normalConfig[$key]);
            $normalConfig[$key] = Hash::merge($defaultConfig, $normalConfig[$key]);
        }

        return $normalConfig;
    }

/**
 * alias for CustomValidatorBehavior::getValidator()
 */
    public function getCustomValidator(Model $Model, $validator = null) {
        return $this->getValidator($Model, $validator);
    }

/**
 * Return validator config.
 * 
 * @param Model $Model
 * @param string $validator Validator name.
 * @return array Config.
 */
    public function getValidator(Model $Model, $validator = null) {
        if ($validator !== null) {
            return $this->settings[$Model->alias]['validators'][$validator];
        }
        return $this->settings[$Model->alias]['validators'];
    }

/**
 * Save custom validator fields config.
 * 
 * @param Model $Model
 * @param string $validator Validator name.
 * @param mixed $data Request data.
 * @return boolean Success.
 */
    public function saveCustomValidator(Model $Model, $validator, $data) {
        $validatorConfig = $this->getValidator($Model, $validator);

        $CustomValidatorField = ClassRegistry::init('CustomValidator.CustomValidatorField');

        $ret = true;

        foreach ($data as $field => $value) {
            if (!isset($validatorConfig['fields'][$field])) {
                continue;
            }

            $ret &= (boolean) $CustomValidatorField->saveCustomValidatorField($Model->alias, $validator, $field, $value);
        }

        //clean up, delete all fields, that are not in validator field list
        $ret &= (boolean) $CustomValidatorField->deleteCustomValidatorFields(
            $Model->alias, 
            $validator, 
            array_keys($validatorConfig['fields'])
        );

        return $ret;
    }

/**
 * Return validator field data formated for view (Validator setting fields inputs).
 * 
 * @param Model $Model
 * @param string $validator Validator name.
 * @return array Formated Data.
 */
    public function getCustomValidatorData(Model $Model, $validator) {
        $validatorConfig = $this->getValidator($Model, $validator);

        $CustomValidatorField = ClassRegistry::init('CustomValidator.CustomValidatorField');
        $data = $CustomValidatorField->getCustomValidatorField($Model->alias, $validator);

        $formatedData = $validatorConfig['fields'];

        foreach ($data as $field) {
            if (!isset($validatorConfig['fields'][$field['CustomValidatorField']['field']])) {
                continue;
            }

            $formatedData[$field['CustomValidatorField']['field']] = $CustomValidatorField->getOptionValue($field);
        }

        return $formatedData;
    }

/**
 * Return custom validators config with user settings.
 * 
 * @param Model $Model
 * @return array Validator config.
 */
    public function getCustomValidatorConfigData(Model $Model) {
        $validatorsConfig = $this->getValidator($Model);

        foreach ($validatorsConfig as $validator => $config) {
            $validatorsConfig[$validator]['fields'] = $this->getCustomValidatorData($Model, $validator);
        }

        return $validatorsConfig;
    }

/**
 * Find validator for given data. Finding match of given data with validator conditions.
 * 
 * @param Model $Model
 * @param array $data Request/Model data.
 * @return string|false Validator name|false - no match.
 */
    public function findValidator(Model $Model, $data) {
        if (!isset($data[$Model->alias])) {
            return false;
        }
        $modelData = $data[$Model->alias];

        $validators = $this->getValidator($Model);

        foreach ($validators as $validatorName => $validator) {
            $match = true;
            foreach ($validator['conditions'] as $field => $value) {
                if (!isset($modelData[$field]) || $modelData[$field] != $value) {
                    $match &= false;
                }
            }
            if ($match) {
                return $validatorName;
            }
        }

        return false;
    }

/**
 * Set validation of validator on model instance. Find matching validator for given data.
 * 
 * @param Model $Model
 * @param array $data Request/Model data.
 * @return void
 */
    public function setCustomValidator(Model $Model, $data) {
        $validator = $this->findValidator($Model, $data);

        if (empty($validator)) {
            return;
        }

        $this->setCustomValidation($Model, $validator);
    }

/**
 * Apply validation of custom validator.
 * 
 * @param Model $Model
 * @param string $validator Validator name.
 * @return void
 */
    public function setCustomValidation(Model $Model, $validator) {
        $validatorConfig = $this->getValidator($Model, $validator);
        $CustomValidatorField = ClassRegistry::init('CustomValidator.CustomValidatorField');

        foreach ($validatorConfig['fields'] as $field => $config) {
            $value = $config;

            $storedField = $CustomValidatorField->getCustomValidatorField($Model->alias, $validator, $field);

            if (!empty($storedField)) {
                $value = $CustomValidatorField->getOptionValue($storedField);
            }

            if (isset(CustomValidatorField::getValidations()[$value])) {
                $Model->validate[$field][$value] = CustomValidatorField::getValidations()[$value];
            }
            else {
                unset($Model->validate[$field]);
            }
        }
    }

/**
 * Set validation of validator to FieldDataCollection.
 */
    public function setCustomValidatorToCollection(FieldDataCollection $Collection, $data) {
        $validator = $this->findValidator($Collection->getModel(), $data);

        if (empty($validator)) {
            return;
        }

        $this->setCustomValidationToCollection($Collection, $validator);
    }

/**
 * Apply validation of custom validator.
 */
    public function setCustomValidationToCollection(FieldDataCollection $Collection, $validator) {
        $Model = $Collection->getModel();

        $validatorData = $this->getCustomValidatorData($Model, $validator);

        foreach ($validatorData as $field => $value) {
            $this->setValidationToFieldData($Collection->{$field}, $value);
        }
    }

/**
 * Apply validation data to FieldDataEntity.
 */
    public function setValidationToFieldData(FieldDataEntity $FieldData, $validationValue)
    {
        if ($validationValue == CustomValidatorField::DISABLED_VALUE) {
            $FieldData->toggleEditable(false);
        }
        elseif ($validationValue == CustomValidatorField::OPTIONAL_VALUE) {
            $FieldData->addValidation(['mandatory' => false]);
        }
        else {
            $FieldData->addValidation(['mandatory' => true]);
        }
    }

/**
 * Get validator FieldDataCollection for setup form.
 * 
 * @param Model $Model
 * @param string $validator Validator name.
 * @return FieldDataCollection
 */
    public function getValidatorFieldDataCollection($Model, $validator)
    {
        $CustomValidatorField = ClassRegistry::init('CustomValidator.CustomValidatorField');

        $fieldDataConfig = [];
        $config = $this->getValidator($Model, $validator);

        foreach ($config['fields'] as $field => $value) {
            $FieldData = $Model->getFieldDataEntity($field);

            $fieldDataConfig[$field] = [
                'label' => $FieldData->label(),
                'type' => 'select',
                'editable' => true,
                'group' => 'default',
                'options' => CustomValidatorField::getValidationOptionsFromEntity($FieldData)
            ];
        }

        $Collection = new FieldDataCollection($fieldDataConfig, $CustomValidatorField);

        return $Collection;
    }

}
