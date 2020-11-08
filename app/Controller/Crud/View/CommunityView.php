<?php
App::uses('ClassRegistry', 'Utility');
App::uses('CrudView', 'Controller/Crud');

class CommunityView extends CrudView
{

	public $plugin = false;

	public $_moduleDispatcherConfig = null;

	/**
	 * Initialize callback logic that sets Community stuff.
	 * 
	 * @return void
	 */
	public function initialize()
	{
		parent::initialize();

		$this->_setModuleDispatcherConfig();
	}

	public function getModuleDispatcherConfig()
	{
		return $this->_moduleDispatcherConfig;
	}

	public function _setModuleDispatcherConfig()
	{
		$controller = $this->getSubject()->controller;

		$this->_moduleDispatcherConfig = $controller->Crud->config('listeners.ModuleDispatcher.listeners');
	}

}