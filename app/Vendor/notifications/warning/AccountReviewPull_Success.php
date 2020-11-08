<?php
class AccountReviewPull_Success extends NotificationsBase {
	public $filename = 'AccountReviewPull_Success.php';
	public $internal = 'account_review_pull_success';
	public $model = 'AccountReviewPull';
	public $isDefaultType = true;
	public $customEmailTemplate = true;
	public $defaultTypeSettings = array(
		'model' => 'AccountReviewPull',
		'callback' => 'afterSave',
		'type' => 'AccountReviewPullSuccess',
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Pull Completed Successfully');
		$this->description = __('Notifies when a Account Review has been pulled successfully.');
	}
}
