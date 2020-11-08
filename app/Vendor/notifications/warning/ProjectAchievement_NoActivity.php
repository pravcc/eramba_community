<?php
class ProjectAchievement_NoActivity extends ProjectAchievement_NoActivity_Base {
	public $reminderDays = 5;

	public function __construct($options = array()) {
		parent::__construct($options);
	}
}
