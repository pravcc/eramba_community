<?php
App::uses('AbstractAccessType', 'Workflows.Lib');
App::uses('Hash', 'Utility');

class GroupAccessType extends AbstractAccessType {

	public function __construct() {
		
	}

	/**
	 * Get list of users who has assigned a given group
	 * @param  integer           $foreignKey group ID
	 * @param  string $Model.    
	 * @return array             List of users (user_id => user_id)
	 */
	public function process($foreignKey, $Model)
	{
		if (empty($foreignKey)) {
			return [];
		}

		$User = ClassRegistry::init('User');
		$users = $User->find('all', array(
			'contain' => array(
				'Group' => array(
					'fields' => array(
						'Group.id'
					)
				)
			),
			'recursive' => -1
		));

		$usersList = array();
		foreach ($users as $user) {
			$groups = Hash::extract($user, 'Group.{n}.id');
			
			if (in_array($foreignKey, $groups) && !in_array($user['User']['id'], $usersList)) {
				$usersList[$user['User']['id']] = $user['User']['id'];
			}
		}

		return $usersList;
	}
}
