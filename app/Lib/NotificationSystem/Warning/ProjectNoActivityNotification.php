<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');
App::uses('Project', 'Model');

class ProjectNoActivityNotification extends WarningNotification
{
	public function initialize()
	{
		$days = $this->_config['days'];

		$sectionLabel = $this->Model->label([
			'singular' => true
		]);

		$this->_label = __('Project No Activity');

		$this->emailSubject = __(
			'Project %s has not receive updates in the last (%d days)',
			$this->_displayFieldMacro(),
			$days
		);

		$this->emailBody = __('Hello,

We noticed that the project "%s" has not been updated with comments or attachments in the last %s days. Perhaps you should review the project update by clicking on the link below.

%%ITEM_URL%%

Regards',
			$this->_displayFieldMacro(),
			$days
		);
	}

	public function handle($id)
	{
		$days = $this->_config['days'];

		$Project = ClassRegistry::init('Project');

		$data = $Project->find('first', [
			'conditions' => [
				'Project.id' => $id,
				'Project.project_status_id' => Project::STATUS_ONGOING
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

		return date('Y-m-d', strtotime($lastMod)) == date('Y-m-d', strtotime('- ' . $days . 'days'));
	}
}