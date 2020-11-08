<?php
class AwarenessProgramRecurrence extends AppModel {

	public $belongsTo = array(
		'AwarenessProgram'
	);

	public $hasMany = array(
		'AwarenessTraining',
		'AwarenessProgramMissedRecurrence'
	);

}