<?php
App::uses('AppHelper', 'View/Helper');

class WidgetHelper extends AppHelper
{
	public $helpers = ['Html'];
	public $settings = [];
	
	public function getCommentsBadge($Item)
	{
		if (!$Item->Properties->enabled('Widget.Widget')) {
			return '';
		}

		$badge = '';

		if (!empty($Item->unseenCommentsCount())) {
			$badge = $this->Html->tag('span', __('%s new', $Item->unseenCommentsCount()), [
				'class' => ['badge', 'bg-warning-400', 'position-right', 'mr-5', 'text-uppercase']
			]);
		}
		elseif (!empty($Item->commentsCount())) {
			$badge = $this->Html->tag('span', $Item->commentsCount(), [
				'class' => ['badge', 'badge-counter', 'position-right', 'mr-5']
			]);
		}

		return $badge;
	}

	public function getAttachmentsBadge($Item)
	{
		if (!$Item->Properties->enabled('Widget.Widget')) {
			return '';
		}

		$badge = '';

		if (!empty($Item->unseenAttachmentsCount())) {
			$badge = $this->Html->tag('span', __('%s new', $Item->unseenAttachmentsCount()), [
				'class' => ['badge', 'bg-warning-400', 'position-right', 'mr-5', 'text-uppercase']
			]);
		}
		elseif (!empty($Item->attachmentsCount())) {
			$badge = $this->Html->tag('span', $Item->attachmentsCount(), [
				'class' => ['badge', 'badge-counter', 'position-right', 'mr-5']
			]);
		}

		return $badge;
	}

	public function addField($field)
	{
		return $this->_View->element('Widget./../Widget/index');
	}
}