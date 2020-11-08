<?php
App::uses('AppNotificationAppModel', 'AppNotification.Model');

class AppNotification extends AppNotificationAppModel
{
	public $actsAs = [
		'Acl' => [
			'type' => 'controlled'
		],
		'CustomRoles.CustomRoles',
		'Visualisation.Visualisation'
	];

	public $hasMany = [
		'AppNotificationParam' => [
			'className' => 'AppNotification.AppNotificationParam'
		]
	];

	public function parentNode($type)
	{
		if (empty($this->data)) {
			return null;
		}

		if (empty($this->data[$this->alias]['model']) || empty($this->data[$this->alias]['foreign_key'])) {
			return null;
		}

		return [
			$this->data[$this->alias]['model'] => [
				'id' => $this->data[$this->alias]['foreign_key']
			]
		];
	}

	/**
	 * Create and save app notification.
	 * 
	 * @param array $data AppNotification data.
	 * @param array  $params AppNotification params data in [key => value] format.
	 * @return boolean
	 */
	public function saveAppNotification($data, $params = [])
	{
		$paramsData = [];

		foreach ($params as $key => $value) {
			$paramsData[] = [
				'key' => $key,
				'value' => $value
			];
		}

		$ret = $this->saveAssociated([
			'AppNotification' => $data,
			'AppNotificationParam' => $paramsData
		]);

		Cache::clearGroup('app_notification', 'app_notification');
		// Cache::clearGroup('Visualisation', 'visualisation');
		
		return $ret;
	}

	/**
	 * Check if notification exists.
	 * 
	 * @param string $notification Notification class name.
	 * @param string $model Subject model full name with plugin prefix.
	 * @param int $foreignKey Subject id.
	 * @param boolean $excludeExpired Exclude expired notifications from check.
	 * @return boolean
	 */
	public function appNotificationExists($notification, $model, $foreignKey, $excludeExpired = false)
	{
		$conditions = [
			'AppNotification.notification' => $notification,
			'AppNotification.model' => $model,
			'AppNotification.foreign_key' => $foreignKey,
		];

		if ($excludeExpired) {
			$conditions['OR'] = [
				'AppNotification.expiration IS NULL',
				'AppNotification.expiration >=' => date('Y-m-d H:i:s')
			];
		}

		return (boolean) $this->find('count', [
			'conditions' => $conditions,
			'recursive' => -1
		]);
	}

	/**
	 * Remove notification by subject model and foreign key.
	 * 
	 * @param string $notification Notification class name.
	 * @param string $model Subject model full name with plugin prefix.
	 * @param int $foreignKey Subject id.
	 * @return boolean
	 */
	public function removeAppNotificationsBySubject($notification, $model, $foreignKey)
	{
		Cache::clearGroup('app_notification', 'app_notification');
		// Cache::clearGroup('Visualisation', 'visualisation');
		
		return (boolean) $this->deleteAll([
			'AppNotification.notification' => $notification,
			'AppNotification.model' => $model,
			'AppNotification.foreign_key' => $foreignKey
		]);
	}
}
