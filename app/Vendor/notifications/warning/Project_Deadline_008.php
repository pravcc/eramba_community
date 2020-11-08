<?php
class Project_Deadline_008 extends Project_Deadline_Base {
	protected $reminderDays = 30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
