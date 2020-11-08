<?php
class UsersGroup extends AppModel {
	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'User',
		'Group'
	);

	/**
	 * Move groups from users table to users_groups table
	 * @param  string $type up|down (up - move data to users_table | down - move data to group_id)
	 */
	public function moveGroupsToNewDbTable($type)
	{
		if ($type === 'up') {
			$users = $this->User->find('all', array(
				'fields' => array(
					'User.id', 'User.group_id'
				),
				'recursive' => -1
			));
			$data = [];
			foreach ($users as $user) {
				$data[] = [
					'user_id' => $user['User']['id'],
					'group_id' => $user['User']['group_id']
				];
			}

			$this->saveMany($data);
		} elseif ($type === 'down') {
			$groups = $this->find('all', array(
				'fields' => array(
					'UsersGroup.user_id', 'UsersGroup.group_id'
				),
				'group' => array('UsersGroup.user_id'),
				'order' => array('UsersGroup.id'),
				'recursive' => -1
			));
			
			foreach ($groups as $group) {
				$this->User->id = $group['UsersGroup']['user_id'];

				$data = array(
					'group_id' => $group['UsersGroup']['group_id']
				);
				$this->User->save($data, false, array('group_id'));
			}
		}
	}
}
