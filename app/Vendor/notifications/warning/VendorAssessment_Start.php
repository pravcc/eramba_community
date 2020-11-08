<?php
class VendorAssessment_Start extends NotificationsBase {
	public $filename = 'VendorAssessment_Start.php';
	public $internal = 'vendor_assessment_start';
	public $model = 'VendorAssessment';
	public $isDefaultType = true;
	public $customEmailTemplate = true;
	public $defaultTypeSettings = array(
		'model' => 'VendorAssessment',
		'callback' => 'afterSave',
		'type' => 'VendorAssessmentStart',
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('VA has been Started');
		$this->description = __('Notifies when a VA is started manually or automatically because is schedulled.');
	}
}
