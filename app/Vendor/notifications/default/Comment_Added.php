<?php
class Comment_Added extends NotificationsBase {
	public $filename = 'Comment_Added.php';
	public $internal = 'comment_added';
	public $model = null;
	public $customEmailTemplate = true;

	public $isDefaultType = true;
	public $defaultTypeSettings = array(
		'model' => 'Comment',
		'callback' => 'afterSave'
	);

	public function __construct($options = array()) {
		parent::__construct($options);
		
		$this->title = __('Comments');
		$this->description = __('Notifies about a new comment');
	}
}
