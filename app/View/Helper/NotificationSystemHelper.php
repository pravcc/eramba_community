<?php
App::uses('ErambaHelper', 'View/Helper');
class NotificationSystemHelper extends ErambaHelper {
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function getIndexLink($model) {
		if (is_array($model)) {
			$list = array();
			foreach ($model as $alias => $name) {
				$list[] = $this->Html->link($name, array(
					'plugin' => null,
					'controller' => 'notificationSystem',
					'action' => 'index',
					$alias
				), array(
					'escape' => false
				));
			}

			$ul = $this->Html->nestedList($list, array(
				'class' => 'dropdown-menu pull-right',
				'style' => 'text-align: left;'
			));

			$btn = '<button class="btn dropdown-toggle" data-toggle="dropdown"><i class="icon-cog"></i>' . __('Notifications') . ' <span class="caret"></span></button>';

			return $this->Html->div("btn-group group-merge", $btn . $ul);
		}
		else {
			return $this->Html->div("btn-group group-merge",
				$this->Html->link( '<i class="icon-info-sign"></i>' . __('Notifications'), array(
					'plugin' => null,
					'controller' => 'notificationSystem',
					'action' => 'index',
					$model
				), array(
					'class' => 'btn',
					'escape' => false
				))
			);
		}		
	}

	public function getStatuses($item) {
		$statuses = array();

		if (!empty($item['NotificationObject'])) {
			$statuses[] = $this->getLabel(__('Active notification'), 'info');

			$sent = $ignored = false;
			foreach ($item['NotificationObject'] as $notification) {
				if (!$sent && $notification['status_feedback'] == NOTIFICATION_FEEDBACK_WAITING) {
					//$statuses[] = $this->getLabel(__('Notifications sent'), 'info');
					$sent = true;
				}
				if (!$ignored && $notification['status_feedback'] == NOTIFICATION_FEEDBACK_IGNORE) {
					//$statuses[] = $this->getLabel(__('Notifications ignored'), 'warning');
					$ignored = true;
				}
			}
		}

		return $this->processStatusesGroup($statuses);
	}

}