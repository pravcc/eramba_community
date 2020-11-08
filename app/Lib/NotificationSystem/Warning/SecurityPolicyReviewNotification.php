<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');

class SecurityPolicyReviewNotification extends WarningNotification
{
	public function initialize()
	{
		$sectionLabel = $this->Model->label([
			'singular' => true
		]);

		$this->_label = __('Security Policy Review');

		$this->emailSubject = __(
			'Policy Review for item "%s"',
			$this->_displayFieldMacro()
		);

		$this->emailBody = __('Hello,

On %s, there is a scheduled review for the policy "%s". This is because at certain intervals every policy must be reviewed to ensure is still relevant and its attributes accurate. If you are receiving this email is most likely because you have been assigned as a reviewer.

- Follow the link below and login in eramba with your credentials, you will be redirected to the policy that is missing a review.
- You can then click on the item menu / Reviews, a window will open showing all completed and incomplete reviews
- Edit and complete all mandatory fields for the missing review

%%ITEM_URL%%

Regards',
			$this->Model->getMacroByName('next_review_date'),
			$this->_displayFieldMacro()
		);
	}

	public function handle($id)
	{
		$days = $this->_config['days'];
		$absReminder = abs($days);

		$conds = [
			'SecurityPolicy.id' => $id
		];

		if ($days < 0) {
			$conds['SecurityPolicy.next_review_date'] = date('Y-m-d', strtotime('+' . $absReminder . ' days'));
		}
		else {
			$conds['SecurityPolicy.next_review_date'] = date('Y-m-d', strtotime('-' . $absReminder . ' days'));
		}

		$SecurityPolicy = ClassRegistry::init('SecurityPolicy');
		$count = $SecurityPolicy->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}