<?php
App::uses('AppHelper', 'View/Helper');

class SettingsSubSectionHelper extends AppHelper
{
    public $helpers = array('Html');
    public $settings = array();
    
    public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->settings = $settings;

        $this->_setBreadcrumbs();
    }

    protected function _setBreadcrumbs()
    {
        $viewVars = $this->_View->viewVars;
        if (isset($viewVars['settingsAdditionalBreadcrumbs'])) {
            foreach ($viewVars['settingsAdditionalBreadcrumbs'] as $crumb) {
                $this->Html->addCrumb($crumb['name'], $crumb['link'], $crumb['options']);
            }
        }
    }
}