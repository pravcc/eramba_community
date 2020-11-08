<?php
App::uses('WarningNotification', 'NotificationSystem.Lib/NotificationSystem');

class ReviewExpirationNotification extends WarningNotification
{
	public function initialize()
	{
		$ParentSection = $this->Model->{$this->Model->parentModel()};
		
		$parentLabel = $ParentSection->label([
			'singular' => true
		]);

		$this->_label = __('%s Review', $parentLabel);

		$this->emailSubject = __('Scheduled %s Review for item "%s"', $parentLabel, $this->_displayFieldMacro($ParentSection));
		$this->emailBody = __('Hello,

On %s, there is a scheduled review for the %s "%s". This is because at certain intervals every %s must be reviewed to ensure is still relevant and its attributes accurate. If you are receiving this email is most likely because you have been assigned as a reviewer.

- Follow the link below and login in eramba with your credentials, you will be redirected to the %s that is missing a review.
- You can then click on the item menu / Reviews, a window will open showing all completed and incomplete reviews
- Edit and complete all mandatory fields for the missing review

%%ITEM_URL%%

Regards

			',
			$this->Model->getMacroByName('planned_date'),
			$parentLabel,

			$this->_displayFieldMacro($ParentSection),
			$parentLabel,
			$parentLabel
		);
	}

	public function handle($id)
	{
		$days = $this->_config['days'];
		$absReminder = abs($days);

		$conds = [
			$this->Model->alias . '.id' => $id,
			$this->Model->alias . '.completed' => REVIEW_NOT_COMPLETE
		];

		if ($days < 0) {
			$conds[$this->Model->alias . '.planned_date'] = date('Y-m-d', strtotime('+' . $absReminder . ' days'));
		}
		else {
			$conds[$this->Model->alias . '.planned_date'] = date('Y-m-d', strtotime('-' . $absReminder . ' days'));
		}

		$count = $this->Model->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return (bool) $count;
	}
}