<?php
class VendorAssessment_FirstActivity extends NotificationsBase {
	public $filename = 'VendorAssessment_FirstActivity.php';
	public $internal = 'vendor_assessment_first_activity';
	public $model = 'VendorAssessment';
	public $isDefaultType = true;
	public $customEmailTemplate = true;
	public $defaultTypeSettings = array(
		'model' => 'VendorAssessment',
		'callback' => 'afterSave',
		'type' => 'VendorAssessmentFirstActivity',
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('VA First Answer Submission');
		$this->description = __('Triggers on the first auditee response of a question');
	}
}
