<?php
if (!empty($item['NotificationSystem'])) {
	$sent = $ignored = false;
	foreach ($item['NotificationSystem'] as $notification) {
		if (!$sent && $notification['status_feedback'] == NOTIFICATION_FEEDBACK_WAITING) {
			echo $this->Html->tag('span', __('Notifications sent'), array(
				'class' => 'label label-info'
			));
			$sent = true;
		}
		if (!$ignored && $notification['status_feedback'] == NOTIFICATION_FEEDBACK_IGNORE) {
			echo $this->Html->tag('span', __('Notifications ignored'), array(
				'class' => 'label label-warning'
			));
			$ignored = true;
		}
	}
}
?>