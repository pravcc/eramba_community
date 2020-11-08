<?php
App::uses('AccountReviewFeedback', 'AccountReviews.Model');

class AccountReviewPull_SubmitNotSure extends NotificationsBase {
	public $filename = 'AccountReviewPull_SubmitNotSure.php';
	public $internal = 'account_review_pull_submit';
	public $model = 'AccountReviewPull';
	public $isDefaultType = true;
	public $customEmailTemplate = true;
	public $defaultTypeSettings = array(
		'model' => 'AccountReviewPull',
		'callback' => 'afterSave',
		'type' => 'AccountReviewPullSubmitNotSure',
	);

	public function __construct($options = array()) {
		parent::__construct($options);

		$answer = AccountReviewFeedback::answers()[AccountReviewFeedback::ANSWER_NOT_SURE];
		
		$this->title = __('Account Review Feedback Submitted - %s', $answer);
		$this->description = __('Notifies when a Account Review pull was submitted with "%s" feedback.', $answer);
	}
}
