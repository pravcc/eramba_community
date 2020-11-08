<?php
class AccountReviewPull_Exits extends NotificationsBase {
	public $filename = 'AccountReviewPull_Exits.php';
	public $internal = 'account_review_pull_exits';
	public $model = 'AccountReviewPull';
	public $isDefaultType = true;
	public $customEmailTemplate = true;
	public $defaultTypeSettings = array(
		'model' => 'AccountReviewPull',
		'callback' => 'afterSave',
		'type' => 'AccountReviewPullExits',
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Exits Account Review Pull completed with creeping accounts');
		$this->description = __('Notifies when a Exits Account Review pull has been pulled and there are creeping accounts to review.');
	}
}
