<?php
class SecurityPolicyReview_Begin extends NotificationsBase {
	public $internal = 'security_policy_review_begin';
	public $model = 'SecurityPolicyReview';

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Security Policy Review About to Come');
		$this->description = __('Notifies 30 days before a scheduled Security Policy Review begins');

		$this->conditions = array(
			$this->model . '.planned_date' => date('Y-m-d', strtotime('+30 days'))
		);
	}
}
