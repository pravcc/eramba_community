<?php
App::uses('FieldDataHelper', 'FieldData.View/Helper');

class QuickActionFieldsHelper extends FieldDataHelper {
    public $helpers = array('Form', 'Html', 'Ajax');
    
    protected function _parseOptions(FieldDataEntity $Field, $options = []) {
        $quickAddOptions = $options['data-quick-add'];
        $options['data-quick-add'] = json_encode($options['data-quick-add']);
        $options = parent::_parseOptions($Field, $options);

        $options = am($options, [
            'div' => 'form-group form-group-quick-create',
            'between' => '<div class="col-md-9">',
            'after' => $this->help($Field) . $this->description($Field) . '</div>' . $this->Html->div('col-md-1 quick-create', $this->quickAddBtn($quickAddOptions)),
        ]);

        return $options;
    }

    /**
     * Returns a html formatted tag with quick add link.
     */
    public function quickAddBtn($options) {
        $btn = $this->Ajax->quickAddAction($options);

        return $btn;
    }
}