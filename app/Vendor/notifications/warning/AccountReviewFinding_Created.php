<?php
class AccountReviewFinding_Created extends NotificationsBase {
	public $filename = 'AccountReviewFinding_Created.php';
	public $internal = 'account_review_finding_created';
	public $model = 'AccountReviewFinding';
	public $isDefaultType = true;
	public $customEmailTemplate = true;
	public $defaultTypeSettings = array(
		'model' => 'AccountReviewFinding',
		'callback' => 'afterSave',
		'type' => 'AccountReviewFindingCreated',
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('New account review finding has been created');
		$this->description = __('Notifies when new finding is created for account review.');
	}
}
