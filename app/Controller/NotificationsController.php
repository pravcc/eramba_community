<?php
class NotificationsController extends AppController {
	public $helpers = array('Html');
	public $components = array('Session');

	public function setNotificationsAsSeen() {
		$this->allowOnlyAjax();
		$this->autoRender = false;

		$this->Notification->updateAll(array(
			'Notification.status' => 0,
			'Notification.modified' => "'" . date('Y-m-d H:i:s') . "'"
		), array(
			'Notification.user_id' => $this->logged['id']
		));
	}
}
