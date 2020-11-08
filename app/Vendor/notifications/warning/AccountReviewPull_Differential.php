<?php
class AccountReviewPull_Differential extends NotificationsBase {
	public $filename = 'AccountReviewPull_Differential.php';
	public $internal = 'account_review_pull_differential';
	public $model = 'AccountReviewPull';
	public $isDefaultType = true;
	public $customEmailTemplate = true;
	public $defaultTypeSettings = array(
		'model' => 'AccountReviewPull',
		'callback' => 'afterSave',
		'type' => 'AccountReviewPullDifferential',
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Differential Account Review Pull completed with news');
		$this->description = __('Notifies when a Differential Account Review pull has been pulled and there are new or removed accounts to review.');
	}
}
