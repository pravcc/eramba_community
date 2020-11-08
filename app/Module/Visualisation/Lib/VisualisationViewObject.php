<?php
App::uses('ClassRegistry', 'Utility');

class VisualisationViewObject {

	/**
	 * Model on which all methods should run.
	 * 
	 * @var Model
	 */
	protected $Model;

	/**
	 * Variable holds boolean value that says if Visualisation feature is enabled fur a current Model.
	 * 
	 * @var boolean
	 */
	protected $_isEnabled;

	public function __construct(Model $Model)
	{
		$this->Model = $Model;

		$this->_setConfiguration();
	}

	public function getModel()
	{
		return $this->Model;
	}

	protected function _setConfiguration()
	{
		$VisualisationSetting = ClassRegistry::init('Visualisation.VisualisationSetting');
		$this->_isEnabled = $VisualisationSetting->isEnabled($this->Model->alias);
	}

	public function isEnabled()
	{
		return $this->_isEnabled;
	}

}
