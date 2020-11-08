<?php
class ProjectAchievement_NoActivity_Base extends NotificationsBase {
	public $internal = 'project_achievement_no_activity';
	public $model = 'ProjectAchievement';
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
		$this->description = __('No updates (on the task, comments or attachments) have been included for the last %s days', $absReminder);
	}

	public function triggerCheck($id) {
		$ProjectAchievement = ClassRegistry::init('ProjectAchievement');

		$data = $ProjectAchievement->find('first', [
			'conditions' => [
				'ProjectAchievement.id' => $id,
				'ProjectAchievement.completion <' => 100,
				'Project.project_status_id' => PROJECT_STATUS_ONGOING,
			],
			'fields' => [
				'ProjectAchievement.id',
				'ProjectAchievement.modified',
				'Project.id'
			],
			'contain' => [
				'Project',
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

		$lastMod = $data['ProjectAchievement']['modified'];

		if (!empty($data['Comment'][0]['created']) && $data['Comment'][0]['created'] > $lastMod) {
			$lastMod = $data['Comment'][0]['created'];
		}

		if (!empty($data['Attachment'][0]['created']) && $data['Attachment'][0]['created'] > $lastMod) {
			$lastMod = $data['Attachment'][0]['created'];
		}

		return date('Y-m-d', strtotime($lastMod)) == date('Y-m-d', strtotime('- ' . $this->reminderDays . 'days'));
	}
}
