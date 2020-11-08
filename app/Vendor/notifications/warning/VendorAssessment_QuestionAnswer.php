<?php
class VendorAssessment_QuestionAnswer extends NotificationsBase {
	public $filename = 'VendorAssessment_QuestionAnswer.php';
	public $internal = 'vendor_assessment_question_answer';
	public $model = 'VendorAssessment';
	public $isDefaultType = true;
	public $customEmailTemplate = true;
	public $defaultTypeSettings = array(
		'model' => 'VendorAssessment',
		'callback' => 'afterSave',
		'type' => 'VendorAssessmentQuestionAnswer',
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Questionnaire Answer Responded');
		$this->description = __('Triggers every time an question is responded');
	}
}
