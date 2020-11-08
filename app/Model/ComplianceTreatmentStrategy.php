<?php
class ComplianceTreatmentStrategy extends AppModel {
    public $displayField = 'name';

    public $actsAs = array(
        'Containable'
    );

    /*
     * static enum: Model::function()
     * @access static
     */
    public static function strategies($value = null) {
        $options = array(
            self::STRATEGY_COMPLIANT => __('Compliant'),
            self::STRATEGY_NOT_APPLICABLE => __('Not Applicable'),
            self::STRATEGY_NOT_COMPLIANT => __('Not Compliant'),
        );
        return parent::enum($value, $options);
    }

    const STRATEGY_COMPLIANT = 1;
    const STRATEGY_NOT_APPLICABLE = 2;
    const STRATEGY_NOT_COMPLIANT = 3;
}