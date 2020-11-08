<?php
class ProjectAchievement_Deadline_001 extends ProjectAchievement_Deadline_Base {
	protected $reminderDays = -10;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
