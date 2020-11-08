<?php
class Attachment_Added extends NotificationsBase {
	public $filename = 'Attachment_Added.php';
	public $internal = 'attachment_added';
	public $model = null;
	public $customEmailTemplate = true;

	public $isDefaultType = true;
	public $defaultTypeSettings = array(
		'model' => 'Attachment',
		'callback' => 'afterSave'
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Attachments');
		$this->description = __('Notifies about a new attachment');
	}
}
