<?php
App::uses('ModuleBase', 'Lib');
App::uses('ClassRegistry', 'Utility');

class UserFieldsModule extends ModuleBase
{
	/**
	 * Sync existing objects.
	 *
	 * @return boolean
	 **/
	public function syncExistingObjects()
	{
		return ClassRegistry::init('UserFields.UserFieldsObject')->syncExistingObjects();
	}
}
