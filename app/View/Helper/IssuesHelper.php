<?php
App::uses('AppHelper', 'View/Helper');

class IssuesHelper extends AppHelper {
    public $helpers = ['Html', 'Ajax', 'Eramba', 'FieldData.FieldData', 'Limitless.Alerts', 'FormReload'];
    public $settings = array();
    
    public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);

        $this->settings = $settings;
    }

    public function foreignKeyField(FieldDataEntity $Field)
    {
        return $this->FieldData->input($Field, $this->FormReload->triggerOptions());
    }

}