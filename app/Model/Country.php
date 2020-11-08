<?php
App::uses('AppModel', 'Model');
App::uses('I18nCountry', 'Utils.Lib');

class Country extends AppModel {

    public $displayField = 'country_id';

    public $actsAs = [
        'Containable'
    ];

    const TYPE_DATA_ASSET_SETTING_SUPERVISORY_AUTHORITY = 1;
    const TYPE_DATA_ASSET_GDPR_THIRD_PARTY_INVOLVED = 2;

    public function __construct($id = false, $table = null, $ds = null)
    {
        $this->label = __('Country');

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
        ];

        $this->fieldData = [
            'country_id' => [
                'label' => __('Country'),
                'editable' => true,
                'options' => [$this, 'countries'],
            ],
        ];

        parent::__construct($id, $table, $ds);
    }

    public static function countries($value = null) {
        $countries = new I18nCountry();
        $list = $countries->getList();

        return parent::enum($value, $list);
    }

    public static function europeCountries($value = null) {
        $euCountryCodes = [
            'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 
            'IS', 'IE', 'IT', 'LV', 'LI', 'LU', 'LT', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 
            'SK', 'SI', 'ES', 'SE', 'CH', 'GB'
        ];

        $list = array_intersect_key(static::countries($value), array_flip($euCountryCodes));

        return parent::enum($value, $list);
    }
}
