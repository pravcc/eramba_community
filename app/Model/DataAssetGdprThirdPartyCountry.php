<?php
App::uses('AppModel', 'Model');

class DataAssetGdprThirdPartyCountry extends AppModel {

    public $displayField = 'third_party_country';

    /*
     * static enum: Model::function()
     * @access static
     */
    public static function thirdPartyCountries($value = null) {
        $options = array(
            1 => __('Transfers in between Public Authorities'),
            2 => __('Binding Corporate Rules (BCR)'),
            3 => __('Model Clauses'),
            4 => __('DPA Clauses'),
            5 => __('Code of Conduct'),
            6 => __('Certifications'),
            7 => __('Ad-Hoc Contracts'),
            8 => __('Administrative Arrangements'),
            9 => __('Court Judgements'),
            10 => __('Data Subject Consent'),
            11 => __('Contracts in between Data Subject and Controller'),
            12 => __('Interest of the Data Subject'),
            13 => __('Public Interest'),
            14 => __('Legal Proceedings'),
            15 => __('Vital Interests'),
            16 => __('Registers'),
            17 => __('Controller Compelling Legitimate Interest'),
        );
        return parent::enum($value, $options);
    }

    public function __construct($id = false, $table = null, $ds = null)
    {
        $this->label = __('Data Asset GDPR Third Party Country');

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
        ];

        $this->fieldData = [
            'third_party_country' => [
                'label' => __('Lawful Base for Transfers outside EEA'),
                'editable' => true,
                'options' => [$this, 'thirdPartyCountries'],
            ],
        ];

        parent::__construct($id, $table, $ds);
    }

}
