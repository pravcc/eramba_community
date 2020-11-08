<?php
App::uses('ClassRegistry', 'Utility');
App::uses('CrudView', 'Controller/Crud');

class VisualisationView extends CrudView
{

    /**
     * Variable holds boolean value that says if Visualisation feature is enabled fur a current Model.
     * 
     * @var boolean
     */
    protected $_isEnabled;

    /**
     * Initialize callback logic that sets the trash counter.
     * 
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->_setConfiguration();
    }

    protected function _setConfiguration()
    {
        $VisualisationSetting = ClassRegistry::init('Visualisation.VisualisationSetting');
        $this->_isEnabled = $VisualisationSetting->isEnabled($this->getSubject()->model->alias);
    }

    public function isEnabled()
    {
        return $this->_isEnabled;
    }

}
