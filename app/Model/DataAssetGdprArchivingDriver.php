<?php
App::uses('AppModel', 'Model');

class DataAssetGdprArchivingDriver extends AppModel {

    public $displayField = 'archiving_driver';
    
    /*
     * static enum: Model::function()
     * @access static
     */
    public static function archivingDrivers($value = null) {
        $options = array(
            self::PUBLIC_INTEREST => __('Public Interest'),
            self::SCIENTIFIC => __('Scientific'),
            self::HISTORICAL => __('Historical'),
            self::STATISTICAL_PURPOSES => __('Statistical Purposes'),
        );
        return parent::enum($value, $options);
    }

    const PUBLIC_INTEREST = 1;
    const SCIENTIFIC = 2;
    const HISTORICAL = 3;
    const STATISTICAL_PURPOSES = 4;

    public function __construct($id = false, $table = null, $ds = null)
    {
        $this->label = __('Data Asset GDPR Archiving Driver');

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
        ];

        $this->fieldData = [
            'archiving_driver' => [
                'label' => __('Archiving Driver'),
                'editable' => true,
                'options' => [$this, 'archivingDrivers'],
            ],
        ];

        parent::__construct($id, $table, $ds);
    }
}
