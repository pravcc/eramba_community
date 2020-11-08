<?php
class Project_Deadline_006 extends Project_Deadline_Base {
	protected $reminderDays = 10;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
