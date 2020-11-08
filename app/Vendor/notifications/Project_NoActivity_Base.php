<?php
class Project_NoActivity_Base extends NotificationsBase {
	public $internal = 'project_no_activity';
	public $model = 'Project';
	public $reminderDays = null;
	public $customEmailTemplate = true;

	public function __construct($options = array()) {
		parent::__construct($options);

		if ($this->reminderDays === null) {
			return false;
		}

		$days = $this->reminderDays;

		// always positive number
		$absReminder = abs($days);
		
		$this->title = __('No activity in the last %s Days', $absReminder);
		$this->description = __('No updates on the project for the last %s days', $absReminder);
	}

	public function triggerCheck($id) {
		$Project = ClassRegistry::init('Project');

		$data = $Project->find('first', [
			'conditions' => [
				'Project.id' => $id,
				'Project.project_status_id' => PROJECT_STATUS_ONGOING
			],
			'fields' => [
				'Project.id', 'Project.modified'
			],
			'contain' => [
				'Comment' => [
					'fields' => ['Comment.id', 'Comment.created'],
					'order' => ['Comment.created' => 'DESC'],
					'limit' => 1
				],
				'Attachment' => [
					'fields' => ['Attachment.id', 'Attachment.created'],
					'order' => ['Attachment.created' => 'DESC'],
					'limit' => 1
				]
			],
		]);

		if (empty($data)) {
			return false;
		}

		$lastMod = $data['Project']['modified'];

		if (!empty($data['Comment'][0]['created']) && $data['Comment'][0]['created'] > $lastMod) {
			$lastMod = $data['Comment'][0]['created'];
		}

		if (!empty($data['Attachment'][0]['created']) && $data['Attachment'][0]['created'] > $lastMod) {
			$lastMod = $data['Attachment'][0]['created'];
		}

		return date('Y-m-d', strtotime($lastMod)) == date('Y-m-d', strtotime('- ' . $this->reminderDays . 'days'));
	}
}
