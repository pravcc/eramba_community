<?php
App::uses('ClassRegistry', 'Utility');
App::uses('CrudView', 'Controller/Crud/View');

class BulkActionsView extends CrudView
{
	public function initialize()
	{
		parent::initialize();

		$this->_controller()->helpers[] = 'BulkActions.BulkActions';
	}
	/**
	 * Check if import tool is enabled on section.
	 * 
	 * @return boolean
	 */
	public function enabled()
	{
		$Trash = $this->_controller()->View->get('Trash');

		// if the current action is a Trash action, dont show bulk actions feature
		if (($Trash instanceof TrashView && !$Trash->isTrash()) || $Trash == null) {
			return true;
		}

		return false;
	}

	public function isBulkRequest()
	{
		return $this->_listener('BulkActions')->isBulkRequest();
	}
}
