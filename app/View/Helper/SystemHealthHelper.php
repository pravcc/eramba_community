<?php
App::uses('AppHelper', 'View/Helper');
App::Uses('CakeNumber', 'Utility');
App::Uses('CakeTime', 'Utility');
App::uses('CakeSession', 'Model/Datasource');
App::uses('SystemHealthLib', 'Lib');

class SystemHealthHelper extends AppHelper {
    public $helpers = array('Html', 'Text', 'Label');
    public $settings = array();

    public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->settings = $settings;
    }

    /**
     * Shows a title for the check.
     */
    public function title($check) {
        $title = $check['name'];
        if (isset($check['new']) && $check['new'] === true) {
            $title = $this->Label->primary(__('New')) . ' ' . $title;
        }

        return $title;
    }

    /**
     * Shows a label for criticality of the check.
     */
    public function criticality($value) {
        return SystemHealthLib::getSystemHealthCriticality($value);
    }

    /**
     * Get the status tag for the system health check.
     * 
     * @param  int $value System health check result
     * @return string     Rendered label.
     */
    public function status($value) {
        $label = SystemHealthLib::getSystemHealthStatuses($value);
        $type = $this->label($value);

        return $this->Label->{$type}($label);
    }

    /**
     * Get the label type for a system health check.
     * 
     * @param  int $value System health check result.
     * @return string     Type of the label.
     */
    public function label($value) {
        $types = array(
            SystemHealthLib::SYSTEM_HEALTH_NOT_OK => 'error',
            SystemHealthLib::SYSTEM_HEALTH_OK => 'success'
        );

        return $types[$value];
    }
}