<?php
class AccountReviewPull_Fail extends NotificationsBase {
	public $filename = 'AccountReviewPull_Fail.php';
	public $internal = 'account_review_pull_fail';
	public $model = 'AccountReviewPull';
	public $isDefaultType = true;
	public $customEmailTemplate = true;
	public $defaultTypeSettings = array(
		'model' => 'AccountReviewPull',
		'callback' => 'afterSave',
		'type' => 'AccountReviewPullFail',
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Pull Could not be Completed');
		$this->description = __('Notifies when a Account Review pull could be completed because of some error.');
	}
}
