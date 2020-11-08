<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');

class AssetExpirationNotification extends WarningNotification
{
	public function initialize()
	{
		$this->_label = __('Asset Review');
		$this->emailSubject = __('Scheduled Asset Review for item "%s"', $this->_displayFieldMacro());
		$this->emailBody = __('Hello,

On %s, there is a scheduled review for the asset "%s". This is because at certain intervals every asset must be reviewed to ensure is still relevant and its attributes accurate. If you are receiving this email is most likely because you have been assigned as a reviewer.

- Follow the link below and login in eramba with your credentials, you will be redirected to the asset that is missing a review.
- You can then click on the item menu / Reviews, a window will open showing all completed and incomplete reviews
- Edit and complete all mandatory fields for the missing review

%%ITEM_URL%%

Regards

			',
			$this->Model->getMacroByName('review'),
			$this->_displayFieldMacro()
		);
	}

	public function handle($id)
	{
		$days = $this->_config['days'];
		$absReminder = abs($days);

		$conds = [
			'Asset.id' => $id
		];

		if ($days < 0) {
			$conds['Asset.review'] = date('Y-m-d', strtotime('+' . $absReminder . ' days'));
		}
		else {
			$conds['Asset.review'] = date('Y-m-d', strtotime('-' . $absReminder . ' days'));
		}

		$Asset = ClassRegistry::init('Asset');
		$count = $Asset->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}