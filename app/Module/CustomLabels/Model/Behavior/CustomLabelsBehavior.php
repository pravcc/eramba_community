<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');
App::uses('CustomLabel', 'CustomLabels.Model');
App::uses('CakeEvent', 'Event');

/**
 * CustomLabelsBehavior
 */
class CustomLabelsBehavior extends ModelBehavior
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
     * Setup.
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

        $Model->getEventManager()->attach(function(CakeEvent $event) {
            $this->_setFieldDataLabels($event->subject);
        }, 'Model.afterFieldData');
    }

    /**
     * Find and set FieldData custom labels.
     * 
     * @param Model $Model
     * @return void
     */
    protected function _setFieldDataLabels(Model $Model)
    {
        $customLabels = ClassRegistry::init('CustomLabels.CustomLabel')
            ->getCusomLables(CustomLabel::TYPE_FIELD_DATA, $Model->modelFullName());

        foreach ($customLabels as $customLabel) {
            $fieldDataName = $customLabel['CustomLabel']['subject'];

            if (!$Model->Behaviors->FieldData->hasFieldDataEntity($Model, $fieldDataName)) {
                continue;
            }

            $Field = $Model->Behaviors->FieldData->getFieldDataEntity($Model, $fieldDataName);

            if ($customLabel['CustomLabel']['label'] !== null && trim($customLabel['CustomLabel']['label']) != '') {
                $Field->config('originalLabel', $Field->config('label'));

                $newLabel = (Configure::read('debug')) ? "{$customLabel['CustomLabel']['label']} ({$Field->config('label')})" :  $customLabel['CustomLabel']['label'];
                $Field->config('label', $newLabel);
            }

            if ($customLabel['CustomLabel']['description'] !== null && trim($customLabel['CustomLabel']['description']) != '') {
                $Field->config('description', $customLabel['CustomLabel']['description']);
            }
        }
    }

    /**
     * Construct data for section edit form.
     * 
     * @param Model $Model
     * @return array Custom labels form data.
     */
    public function getCustomLabelsFormData(Model $Model)
    {
        $customLabels = ClassRegistry::init('CustomLabels.CustomLabel')
            ->getCusomLables(CustomLabel::TYPE_FIELD_DATA, $Model->modelFullName());

        $savedData = Hash::combine($customLabels, '{n}.CustomLabel.subject', '{n}.CustomLabel');

        $data = [];

        $Collection = $Model->getFieldCollection();

        foreach ($Collection as $Field) {
            if ($Field->config('UserField')) {
                $fieldName = $Field->getFieldName();

                $originalLabel = (!empty($Field->config('originalLabel'))) ? $Field->config('originalLabel') : $Field->getLabel();

                $item = [
                    'id' => null,
                    'type' => CustomLabel::TYPE_FIELD_DATA,
                    'model' => $Model->modelFullName(),
                    'subject' => $fieldName,
                    'label' => '',
                    'description' => '',
                    'fieldDataConfig' => [
                        'label' => $originalLabel
                    ]
                ];

                if (isset($savedData[$fieldName])) {
                    $item = array_merge($item, $savedData[$fieldName]);
                }

                $data[] = $item;
            }
        }

        return $data;
    }
}