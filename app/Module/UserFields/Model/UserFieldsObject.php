<?php
App::uses('UserFieldsAppModel', 'UserFields.Model');

class UserFieldsObject extends UserFieldsAppModel
{
	public $useTable = 'user_fields_objects';

	public function syncExistingObjects()
	{
		$this->deleteAll(['1 = 1']);

		$UserFieldsUser = ClassRegistry::init('UserFields.UserFieldsUser');

		$users = $UserFieldsUser->find('all', [
			'recursive' => -1,
		]);

		$ret = true;
		
		foreach ($users as $item) {
			$this->create();
			$ret &= $this->save([
				'model' => $item['UserFieldsUser']['model'],
				'foreign_key' => $item['UserFieldsUser']['foreign_key'],
				'field' => $item['UserFieldsUser']['field'],
				'object_id' => $item['UserFieldsUser']['user_id'],
				'object_key' => 'User-' . $item['UserFieldsUser']['user_id'],
				'object_model' => 'User',
				'created' => $item['UserFieldsUser']['created'],
				'modified' => $item['UserFieldsUser']['modified'],
			]);
		}

		$UserFieldsGroup = ClassRegistry::init('UserFields.UserFieldsGroup');

		$groups = $UserFieldsGroup->find('all', [
			'recursive' => -1,
		]);

		foreach ($groups as $item) {
			$this->create();
			$ret &= $this->save([
				'model' => $item['UserFieldsGroup']['model'],
				'foreign_key' => $item['UserFieldsGroup']['foreign_key'],
				'field' => $item['UserFieldsGroup']['field'],
				'object_id' => $item['UserFieldsGroup']['group_id'],
				'object_key' => 'Group-' . $item['UserFieldsGroup']['group_id'],
				'object_model' => 'Group',
				'created' => $item['UserFieldsGroup']['created'],
				'modified' => $item['UserFieldsGroup']['modified'],
			]);
		}

		return $ret;
	}
}