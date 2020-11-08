<?php
App::uses('AppModel', 'Model');

class DataAssetSettingsThirdParty extends AppModel {

    public $actsAs = [
        'Containable'
    ];

    /*
     * static enum: Model::function()
     * @access static
     */
    public static function types($value = null) {
        $options = [
            self::TYPE_PROCESSOR => __('Processor'),
            self::TYPE_CONTROLLER => __('Controller'),
        ];
        return parent::enum($value, $options);
    }

    const TYPE_PROCESSOR = 'processor';
    const TYPE_CONTROLLER = 'controller';
}
