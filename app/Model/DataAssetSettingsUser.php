<?php
App::uses('AppModel', 'Model');

class DataAssetSettingsUser extends AppModel {

    public $actsAs = [
        'Containable'
    ];

    /*
     * static enum: Model::function()
     * @access static
     */
    public static function types($value = null) {
        $options = [
            self::TYPE_DPO => __('Dpo'),
            self::TYPE_CONTROLLER_REPRESENTATIVE => __('Controller Representative')
        ];
        return parent::enum($value, $options);
    }

    const TYPE_DPO = 'dpo';
    const TYPE_CONTROLLER_REPRESENTATIVE = 'controller-representative';
}
