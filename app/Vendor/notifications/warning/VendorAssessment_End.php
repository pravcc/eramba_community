<?php
class VendorAssessment_End extends NotificationsBase {
	public $filename = 'VendorAssessment_End.php';
	public $internal = 'vendor_assessment_end';
	public $model = 'VendorAssessment';
	public $isDefaultType = true;
	public $customEmailTemplate = true;
	public $defaultTypeSettings = array(
		'model' => 'VendorAssessment',
		'callback' => 'afterSave',
		'type' => 'VendorAssessmentEnd',
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('VA has been stopped');
		$this->description = __('Notifies when a VA has been set to status stop');
	}
}
