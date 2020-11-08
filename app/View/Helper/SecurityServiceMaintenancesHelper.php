<?php
App::uses('AppHelper', 'View/Helper');

class SecurityServiceMaintenancesHelper extends AppHelper {
    public $helpers = array('Html', 'FieldData.FieldData', 'FormReload');
    public $settings = array();

    public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);

        $this->settings = $settings;
    }

    public function securityServiceField(FieldDataEntity $Field)
    {
        return $this->FieldData->input($Field, $this->FormReload->triggerOptions());
    }

    /**
     * result field output filter
     */
    public function outputResult($data, $options = array()) {
        $statuses = getAuditStatuses();
        $value = '';

        if ($data === null || $data === false) {
            $value = __('Incomplete');
        }
        else {
            $value = $statuses[$data];
        }

        return $value;
    }

}