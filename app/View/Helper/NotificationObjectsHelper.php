<?php
App::uses('ErambaHelper', 'View/Helper');
class NotificationObjectsHelper extends ErambaHelper {
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function getStatuses($item) {
		$item = $this->processItemArray($item, 'NotificationObject');

		$statuses = array();

		$statuses[] = $this->getLabel(
			getNotificationsStatuses($item['NotificationObject']['status']),
			getNotificationsStatusColorClass($item['NotificationObject']['status'])
		);

		/*if ($item['NotificationObject']['status_feedback'] == NOTIFICATION_FEEDBACK_WAITING) {
			$statuses[] = $this->getLabel(__('Notifications sent'), 'info');
		}

		if ($item['NotificationObject']['status_feedback'] == NOTIFICATION_FEEDBACK_IGNORE) {
			$statuses[] = $this->getLabel(__('Notifications ignored'), 'warning');
		}*/

		return $this->processStatuses($statuses);
		// return $this->processStatusesGroup($statuses);
	}

}