<?php
App::uses('AppHelper', 'View/Helper');
class ObjectVersionAuditHelper extends AppHelper {
	public $helpers = array('Html', 'Ux');

	public function getTimelineClass(ObjectVersionAuditEvent $Event, $i)
	{
		$class = 'timeline-row';
		if ($i % 2) {
			$class .= ' post-even';
		}

		return $class;
	}

	public function getTimelineIcon(ObjectVersionAuditEvent $Event) {
		$background = 'bg-info-400';
		$icon = 'icon-info3';

		if ($Event->isCreated()) {
			$background = 'bg-success-400';
			$icon = 'icon-plus2';
		} else if ($Event->isEdited()) {
			$background = 'bg-info-400';
			$icon = 'icon-pencil';
		} else if ($Event->isDeleted()) {
			$background = 'bg-danger-400';
			$icon = 'icon-trash-alt';
		} else if ($Event->isRestored()) {
			$background = 'bg-warning-400';
			$icon = 'icon-spinner11';
		}

		$divHtml = $this->Html->div($background, '<i class="' . $icon . '"></i>');

		return $divHtml;
	}
}