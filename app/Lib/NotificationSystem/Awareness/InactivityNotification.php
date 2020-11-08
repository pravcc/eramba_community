<?php
App::uses('AwarenessNotification', 'NotificationSystem.Lib/NotificationSystem');

class InactivityNotification extends AwarenessNotification
{
	public function initialize()
	{
		$sectionLabel = $this->Model->label([
			'singular' => true
		]);

		$this->_label = __('Inactivity Notification');

		$this->emailSubject = __(
			'%s "%s" Inactivity warning',
			$sectionLabel,
			$this->_displayFieldMacro()
		);

		$this->emailBody = __('Hello,

A %s under the title of "%s" haven\'t got any activity in the last days? 
Follow the link below to know more about this project.

%%ITEM_URL%%

Regards',
			$sectionLabel,
			$this->_displayFieldMacro()
		);
	}

	protected function _findQuery($id)
	{
		$periodContain = array(
			'conditions' => array(
				'created >=' => $this->triggerPeriod 
			),
			'fields' => array('id', 'created'),
			'order' => array('created' => 'ASC')
		);

		$query = [
			'conditions' => [
				$this->Model->alias . '.id' => $id
			],
			'contain' => [
				'Comment' => $periodContain,
				'Attachment' => $periodContain
			]
		];

		return $query;
	}

	public function handle($id)
	{
		$item = $this->Model->find('first', $this->_findQuery($id));

		// not commented
		$cond1 = empty($item['Comment']);

		// no file uploaded
		$cond2 = empty($item['Attachment']);

		// and not modified in the interval
		$cond3 = isset($item[$this->Model->alias]['modified']);
		$cond3 = $cond3 && ($item[$this->Model->alias]['modified'] <= $this->triggerPeriod);

		// triggers when there is no comment and attachment added and object hasnt been modified
		// in the past $this->triggerPeriod
		if ($cond1 && $cond2 && $cond3) {
			return true;
		}

		return false;
	}
}