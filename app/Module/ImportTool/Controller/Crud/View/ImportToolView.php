<?php
App::uses('ClassRegistry', 'Utility');
App::uses('CrudView', 'Controller/Crud/View');

class ImportToolView extends CrudView
{
	public function initialize()
	{
		parent::initialize();

		$this->_controller()->helpers[] = 'ImportTool.ImportTool';
	}
	/**
	 * Check if import tool is enabled on section.
	 * 
	 * @return boolean
	 */
	public function enabled()
	{
		return !empty($this->getSubject()->model->Behaviors->enabled('ImportTool.ImportTool'));
	}
}
