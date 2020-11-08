<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');

class RiskExpirationNotification extends WarningNotification
{

	public function initialize()
	{
		$sectionLabel = $this->Model->label([
			'singular' => true
		]);

		$this->_label = __('Risk Expiration');

		$this->emailSubject = __(
			'Scheduled %s Review for item "%s"',
			$sectionLabel,
			$this->_displayFieldMacro()
		);

		$this->emailBody = __('Hello,

On %s, there is a scheduled review for the %s "%s". This is because at certain intervals every risk must be reviewed to ensure is still relevant and its attributes accurate. If you are receiving this email is most likely because you have been assigned as a reviewer.

- Follow the link below and login in eramba with your credentials, you will be redirected to the risk that is missing a review.
- You can then click on the item menu / Reviews, a window will open showing all completed and incomplete reviews
- Edit and complete all mandatory fields for the missing review

%%ITEM_URL%%

Regards',
			$sectionLabel,
			$this->Model->getMacroByName('review'),
			$this->_displayFieldMacro()
		);
	}

	public function handle($id)
	{
		$days = $this->_config['days'];
		$absReminder = abs($days);

		$conds = [
			$this->Model->alias . '.id' => $id
		];

		if ($days < 0) {
			$conds[$this->Model->alias . '.review'] = date('Y-m-d', strtotime('+' . $absReminder . ' days'));
		}
		else {
			$conds[$this->Model->alias . '.review'] = date('Y-m-d', strtotime('-' . $absReminder . ' days'));
		}

		$count = $this->Model->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}