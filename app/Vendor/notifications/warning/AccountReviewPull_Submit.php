<?php
App::uses('AccountReviewFeedback', 'AccountReviews.Model');

class AccountReviewPull_Submit extends NotificationsBase {
	public $filename = 'AccountReviewPull_Submit.php';
	public $internal = 'account_review_pull_submit';
	public $model = 'AccountReviewPull';
	public $isDefaultType = true;
	public $customEmailTemplate = true;
	public $defaultTypeSettings = array(
		'model' => 'AccountReviewPull',
		'callback' => 'afterSave',
		'type' => 'AccountReviewPullSubmit',
	);

	public function __construct($options = array()) {
		parent::__construct($options);

		$answer = AccountReviewFeedback::answers()[AccountReviewFeedback::ANSWER_OK];
		
		$this->title = __('Account Review Feedback Submitted - All %s', $answer);
		$this->description = __('Notifies when a Account Review pull was submitted with empty or "%s" feedbacks.', $answer);
	}
}
