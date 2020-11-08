<?php
App::uses('ClassRegistry', 'Utility');
App::uses('CrudView', 'Controller/Crud');

class LdapSyncView extends CrudView
{
	public function initialize()
	{
		parent::initialize();
	}
}