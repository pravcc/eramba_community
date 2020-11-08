<?php
App::uses('ClassRegistry', 'Utility');
App::uses('CrudView', 'Controller/Crud');

class DashboardView extends CrudView
{
	public $plugin = 'Dashboard';

	public function initialize()
	{
		parent::initialize();
	}
}