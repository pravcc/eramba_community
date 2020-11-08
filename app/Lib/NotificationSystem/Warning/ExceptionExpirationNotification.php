<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');
App::uses('PolicyException', 'Model');

class ExceptionExpirationNotification extends WarningNotification
{

	public function initialize()
	{
		$sectionLabel = $this->Model->label([
			'singular' => true
		]);

		$this->_label = __('Exception Expiration');

		$this->emailSubject = __(
			'%s "%s" expiration on %s',
			$sectionLabel,
			$this->_displayFieldMacro(),
			$this->Model->getMacroByName('expiration')
		);

		$this->emailBody = __('Hello,

A %s under the title of "%s" expires on %s and requires your attention.

- Follow the link below and login in eramba with your credentials, you will be redirected to the %s.
- You can then click on the item and edit the exception
- You can either move the expiring date in the future or change the status to "Closed"

%%ITEM_URL%%

Regards',
			$sectionLabel,
			$this->_displayFieldMacro(),
			$this->Model->getMacroByName('expiration'),
			$sectionLabel
		);
	}

	public function handle($id)
	{
		$days = $this->_config['days'];
		$absReminder = abs($days);

		$conds = [
			$this->Model->alias . '.id' => $id,
			$this->Model->alias . '.status !=' => PolicyException::STATUS_CLOSED
		];

		if ($days < 0) {
			$conds[$this->Model->alias . '.expiration'] = date('Y-m-d', strtotime('+' . $absReminder . ' days'));
		}
		else {
			$conds[$this->Model->alias . '.expiration'] = date('Y-m-d', strtotime('-' . $absReminder . ' days'));
		}

		$count = $this->Model->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}