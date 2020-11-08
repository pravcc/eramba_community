<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');
App::uses('Project', 'Model');

class ProjectAchievementNoActivityNotification extends WarningNotification
{

	public function initialize()
	{
		$days = $this->_config['days'];

		$sectionLabel = $this->Model->label([
			'singular' => true
		]);

		$this->_label = __('Project Task No Activity');

		$this->emailSubject = __(
			'Task on project %s has not receive updates in the last (%d days)',
			$this->Model->getMacroByName('project'),
			$days
		);

		$this->emailBody = __('Hello,

A Project under the title of %s has a task with the description:

%s

We are writing to inform you that there has no been updates on this task in the last %s days. You can find the details of this task by clicking on the link below:

%%ITEM_URL%%

Regards',
			$this->Model->getMacroByName('project'),
			$this->Model->getMacroByName('description'),
			$days
		);
	}

	public function handle($id)
	{
		$days = $this->_config['days'];

		$ProjectAchievement = ClassRegistry::init('ProjectAchievement');

		$data = $ProjectAchievement->find('first', [
			'conditions' => [
				'ProjectAchievement.id' => $id,
				'ProjectAchievement.completion <' => 100,
				'Project.project_status_id' => Project::STATUS_ONGOING,
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

		return date('Y-m-d', strtotime($lastMod)) == date('Y-m-d', strtotime('- ' . $days . 'days'));
	}
}