<?php
App::uses('ModuleBase', 'Lib');
App::uses('ClassRegistry', 'Utility');
App::uses('NotificationSystemManager', 'NotificationSystem.Lib');
App::uses('NotificationSystemSubject', 'NotificationSystem.Lib');

class WidgetModule extends ModuleBase
{
	public function digestNotifications()
	{
		$ret = true;

		$date = date('Y-m-d', strtotime('-0 day'));

		$triggeredItems = [];

		$comments = ClassRegistry::init('Comments.Comment')->find('all', [
			'conditions' => [
				'DATE(Comment.created)' => $date
			],
			'contain' => [
				'User'
			],
			'order' => ['Comment.created' => 'ASC']
		]);

		$attachments = ClassRegistry::init('Attachments.Attachment')->find('all', [
			'conditions' => [
				'DATE(Attachment.created)' => $date
			],
			'contain' => [
				'User'
			],
			'order' => ['Attachment.created' => 'ASC']
		]);

		foreach ($comments as $comment) {
			$key = $comment['Comment']['model'] . '-' . $comment['Comment']['foreign_key'];

			if (in_array($key, $triggeredItems)) {
				continue;
			}

			$triggeredItems[] = $key;

			$ret &= (bool) $this->_triggerDigestNotification(
				$comment['Comment']['model'],
				$comment['Comment']['foreign_key'],
				$this->_getCommentsList($comments, $comment['Comment']['model'], $comment['Comment']['foreign_key']),
				$this->_getAttachmentsList($attachments, $comment['Comment']['model'], $comment['Comment']['foreign_key'])
			);
		}

		foreach ($attachments as $attachment) {
			$key = $attachment['Attachment']['model'] . '-' . $attachment['Attachment']['foreign_key'];

			if (in_array($key, $triggeredItems)) {
				continue;
			}

			$triggeredItems[] = $key;

			$ret &= (bool) $this->_triggerDigestNotification(
				$attachment['Attachment']['model'],
				$attachment['Attachment']['foreign_key'],
				$this->_getCommentsList($comments, $attachment['Attachment']['model'], $attachment['Attachment']['foreign_key']),
				$this->_getAttachmentsList($attachments, $attachment['Attachment']['model'], $attachment['Attachment']['foreign_key'])
			);
		}

		return $ret;
	}

	protected function _getCommentsList($comments, $model, $foreignKey)
	{
		$out = '';

		foreach ($comments as $comment) {
			if ($comment['Comment']['model'] == $model && $comment['Comment']['foreign_key'] == $foreignKey) {
				$out .= '<li>' . h($comment['Comment']['message']) . ' (' . h($comment['User']['name']) . ' ' . h($comment['User']['surname']) . ', ' . $comment['Comment']['created'] . ')</li>';
			}
		}

		if (empty($out)) {
			$out = __('No new comments.');
		}
		else {
			$out = '<ul>' . $out . '</ul>';
		}

		return $out;
	}

	protected function _getAttachmentsList($attachments, $model, $foreignKey)
	{
		$out = '';

		foreach ($attachments as $attachment) {
			if ($attachment['Attachment']['model'] == $model && $attachment['Attachment']['foreign_key'] == $foreignKey) {
				$out .= '<li>' . h($attachment['Attachment']['name']) . ' (' . h($attachment['User']['name']) . ' ' . h($attachment['User']['surname']) . ', ' . $attachment['Attachment']['created'] . ')</li>';
			}
		}

		if (empty($out)) {
			$out = __('No new attachments.');
		}
		else {
			$out = '<ul>' . $out . '</ul>';
		}

		return $out;
	}

	protected function _triggerDigestNotification($model, $id, $commentsList, $attachmentsList)
	{
		return (bool) ClassRegistry::init($model)->triggerNotification('digest', $id, [
			'list_of_new_comments' => $commentsList,
			'list_of_new_attachments' => $attachmentsList
		]);
	}
}
