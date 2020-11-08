<?php
App::uses('AppModel', 'Model');

class UserBan extends AppModel
{
	public $belongsTo = [
		'User',
	];

	/**
	 * Get active ban data for $userId.
	 * 
	 * @param int $userId
	 * @return array
	 */
	public function getActiveBan($userId)
	{
		return $this->find('first', [
			'conditions' => [
				'UserBan.user_id' => $userId,
				'UserBan.until >' => date('Y-m-d H:i:s'),
			],
			'recursive' => -1,
			'contain' => []
		]);
	}

	/**
	 * Create a ban for $userId.
	 * 
	 * @param int $userId
	 * @return boolean
	 */
	public function createBan($userId)
	{
		$this->create();

		return $this->save([
    		'user_id' => $userId,
    		'until' => self::getBanTime()
		]);
	}

	/**
	 * Get next ban time expiration.
	 * 
	 * @return string Datetime.
	 */
	public static function getBanTime()
    {
    	return date('Y-m-d H:i:s', strtotime('+' . Configure::read('Eramba.Settings.BRUTEFORCE_BAN_FOR_MINUTES') . 'minutes'));
    }
}
