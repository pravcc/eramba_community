<?php
App::uses('FieldDataHelper', 'FieldData.View/Helper');

class AssetClassificationFieldHelper extends FieldDataHelper {
    public $helpers = ['BulkActions.BulkActionFields', 'Html', 'Form'];
    
    public function inputName($Field, $inputName, $index) {
        return $inputName . '.';
    }

    public function input($Field, $options = array()) {
        $classifications = $Field->AssetClassification->getClassficationsData();

        $ret = '';
        foreach ($classifications as $key => $type) {
            $opts = [
                'type' => 'select',
                'multiple' => false,
                'div' => 'form-group',
                'label' => $type['AssetClassificationType']['name'],
                'options' => [''=>''] + $this->parseTypeOptions($type),
                'data-placeholder' => __('Choose Classification ...')
            ];
            $opts = am($this->BulkActionFields->getCustomOptions($Field), $opts);
            $ret .= parent::input($Field, $opts);
        }

        return $ret;
    }

    public function parseTypeOptions($type) {
        $arr = [];
        foreach ($type['AssetClassification'] as $c) {
            $arr[$c['id']] = sprintf('%s (%d)', $c['name'], $c['value']);
        }

        return $arr;
    }

}