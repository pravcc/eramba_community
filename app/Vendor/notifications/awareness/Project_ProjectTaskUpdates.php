<?php
class Project_ProjectTaskUpdates extends NotificationsBase {
	public $internal = 'project_project_task_updates';
	public $model = 'Project';

	public function __construct($options = array()) {
		parent::__construct($options);

		$this->title = __('Project Task Updates');
		$this->description = __('Notifies when a Project one or more Tasks under this project have not received any comment during the period specified for this notification');

		$_commentContain = array(
			'conditions' => array(
				'created >=' => $this->_periodDateFrom 
			),
			'fields' => array('id', 'created'),
			'order' => array('created' => 'ASC')
		);

		$this->contain = array(
			'Comment' => $_commentContain,
			'ProjectAchievement' => array(
				'fields' => array('id'),
				'Comment' => $_commentContain
			)
		);
	}

	public function parseData($item) {
		if (empty($item['Comment'])) {
			return true;
		}
			
		foreach ($item['ProjectAchievement'] as $task) {
			if (empty($task['Comment'])) {
				return true;
			}
		}

		return false;
	}
}
