<?php
App::uses('AppModel', 'Model');

class DataAssetGdprCollectionMethod extends AppModel {

    public $displayField = 'collection_method';
    
    /*
     * static enum: Model::function()
     * @access static
     */
    public static function collectionMethods($value = null) {
        $options = array(
            self::AUTOMATED => __('Automated'),
            self::MANUAL => __('Manual'),
        );
        return parent::enum($value, $options);
    }

    const AUTOMATED = 1;
    const MANUAL = 2;

    public function __construct($id = false, $table = null, $ds = null)
    {
        $this->label = __('Data Asset GDPR Collection Method');

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
        ];

        $this->fieldData = [
            'collection_method' => [
                'label' => __('Collection Method'),
                'editable' => true,
                'options' => [$this, 'collectionMethods'],
            ],
        ];

        parent::__construct($id, $table, $ds);
    }
}
