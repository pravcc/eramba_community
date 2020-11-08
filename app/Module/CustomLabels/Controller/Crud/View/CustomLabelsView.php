<?php
App::uses('ClassRegistry', 'Utility');
App::uses('CrudView', 'Controller/Crud/View');

class CustomLabelsView extends CrudView
{
	/**
	 * Initialize callback logic that sets the trash counter.
	 * 
	 * @return void
	 */
	public function initialize()
	{
		parent::initialize();
	}
}
