<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');

class ServiceContractExpirationNotification extends WarningNotification
{

	public function initialize()
	{
		$this->_label = __('Support Contract Expiration');

		$this->emailSubject = __(
			'Support Contract "%s" has a deadline on "%s"',
			$this->Model->getMacroByName('name'),
			$this->Model->getMacroByName('end')
		);

		$this->emailBody = __('Hello,

A support contract under the name "%s" has a deadline set for %s. Follow the link below if you want to know more about this finding.

%%ITEM_URL%%

Regards',
			$this->Model->getMacroByName('name'),
			$this->Model->getMacroByName('end')
		);
	}

	public function handle($id)
	{
		$days = $this->_config['days'];
		$absReminder = abs($days);

		$conds = [
			'ServiceContract.id' => $id
		];

		if ($days < 0) {
			$conds['ServiceContract.end'] = date('Y-m-d', strtotime('+' . $absReminder . ' days'));
		}
		else {
			$conds['ServiceContract.end'] = date('Y-m-d', strtotime('-' . $absReminder . ' days'));
		}

		$ServiceContract = ClassRegistry::init('ServiceContract');
		$count = $ServiceContract->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}