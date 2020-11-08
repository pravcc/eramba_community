<?php
class ProjectAchievement_Deadline_010 extends ProjectAchievement_Deadline_Base {
	protected $reminderDays = -30;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
