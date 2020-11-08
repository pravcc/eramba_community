<?php
App::uses('AppModel', 'Model');
App::uses('Portal', 'Model');

class UsersPortal extends AppModel {
	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'User',
		'Portal'
	);

	public function setAccessToMainPortalForAllUsers()
	{
		$users = $this->User->find('all', array(
			'fields' => array(
				'User.id'
			),
			'recursive' => -1
		));
		
		$data = [];
		foreach ($users as $user) {
			$data[] = [
				'user_id' => $user['User']['id'],
				'portal_id' => Portal::PORTAL_MAIN
			];
		}

		$this->saveMany($data);
	}
}