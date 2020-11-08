<?php
class VendorAssessment_Submit extends NotificationsBase {
	public $filename = 'VendorAssessment_Submit.php';
	public $internal = 'vendor_assessment_submit';
	public $model = 'VendorAssessment';
	public $isDefaultType = true;
	public $customEmailTemplate = true;
	public $defaultTypeSettings = array(
		'model' => 'VendorAssessment',
		'callback' => 'afterSave',
		'type' => 'VendorAssessmentSubmit',
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Submitted VA');
		$this->description = __('Triggers when auditees submit a questionnaire');
	}
}
