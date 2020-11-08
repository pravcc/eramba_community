<?php
App::uses('AppNotificationAppModel', 'AppNotification.Model');

class AppNotificationView extends AppNotificationAppModel
{
	/**
	 * Create or update of existing view record. Set view datetime for user.
	 * 
	 * @param int $userId
	 * @param string $notificationsView Datetime in Y-m-d H:i:s format.
	 * @return boolean
	 */
	public function createOrUpdateView($userId, $notificationsView)
	{
		$item = $this->find('first', [
			'conditions' => [
				'AppNotificationView.user_id' => $userId,
			],
			'fields' => ['AppNotificationView.id'],
			'recursive' => -1,
		]);

		$data = [
			'notifications_view' => $notificationsView,
		];

		if (empty($item)) {
			$data['user_id'] = $userId;
		}
		else {
			$data['id'] = $item['AppNotificationView']['id'];
		}

		$this->create();
		$ret = $this->save(['AppNotificationView' => $data]);

		Cache::clearGroup('app_notification', 'app_notification');
		
		return $ret;
	}
}
