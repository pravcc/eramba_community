<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');
App::uses('CustomField', 'CustomFields.Model');
App::uses('ImportToolModule', 'ImportTool.Lib');

class ImportToolBehavior extends ModelBehavior
{
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
    public function setup(Model $Model, $settings = [])
    {
        if (!isset($this->settings[$Model->alias])) {
            $this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
        }
    }

    /**
     * Get ImportTool args from model settings function.
     * 
     * @param Model $Model
     * @return array ImportTool args.
     */
    public function getImportArgs(Model $Model)
    {
        $args = [];

        if ($Model->hasMethod('getImportToolConfig')) {
            $args = $Model->getImportToolConfig();

            // if custom fields are enabled set custom fields import args
            if ($Model->Behaviors->enabled('CustomFields.CustomFields')) {
                $customFieldsArgs = $this->_getCustomFieldsImportArgs($Model);
                $args = array_merge($args, $customFieldsArgs);
            }
        }

        return $args;
    }

    /**
     * Get CustomFields import args.
     * 
     * @param Model $Model
     * @return array CustomFields import args.
     */
    protected function _getCustomFieldsImportArgs(Model $Model)
    {
        $args = [];

        $FieldDataCollection = $Model->getFieldCollection();

        foreach ($FieldDataCollection as $Field) {
            $customField = $Field->config('CustomFields');

            if (empty($customField)) {
                continue;
            }

            // mandatory prefix
            $mandatoryPrefix = __('OPTIONAL: ');
            if ($customField['mandatory']) {
                $mandatoryPrefix = __('MANDATORY: ');
            }

            // options helper text for TYPE_DROPDOWN
            $optionsHelper = '';
            if ($customField['type'] == CustomField::TYPE_DROPDOWN) {
                $dropdownOptions = [];
                foreach ($customField['CustomFieldOption'] as $fieldOption) {
                    $dropdownOptions[] = $fieldOption['value'];
                }
                $optionsHelper = __('Select one of the following values: %s. ', implode(', ', $dropdownOptions));
            }

            $dateHelper = '';
            if ($customField['type'] == CustomField::TYPE_DATE) {
                $dateHelper = __('Use date in YYYY-MM-DD format. ');
            }

            $fieldConfig = [
                'name' => $customField['name'],
                'headerTooltip' => $mandatoryPrefix . $optionsHelper . $dateHelper .  $customField['description']
            ];

            $key = "CustomFieldValue.{$customField['id']}.value";

            $args[$key] = $fieldConfig;
        }

        return $args;
    }
}
