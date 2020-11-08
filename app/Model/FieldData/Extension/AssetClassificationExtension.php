<?php
App::uses('FieldDataExtension', 'FieldData.Model/FieldData');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class AssetClassificationExtension extends FieldDataExtension {
    protected $_classificationData = null;

    public function setup(FieldDataEntity $field, $config = []) {
    }

    public function initialize(FieldDataEntity $Field) {
        $this->Field = $Field;

        $model = $Field->getModelName();
        $Model = ClassRegistry::init($model);

        $Field->toggleEditable(true);
        // $Field->config('type', FieldDataEntity::FIELD_TYPE_SELECT);

        $classifications = $Model->AssetClassification->AssetClassificationType->find('all', array(
            'order' => array('AssetClassificationType.name' => 'ASC'),
            'recursive' => 1
        ));

        $this->_classificationData = $classifications;
    }

    public function getClassficationsData() {
        return $this->_classificationData;
    }

}