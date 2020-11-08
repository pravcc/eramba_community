<?php
class AwarenessProgramMissedRecurrence extends AppModel {
	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'AwarenessProgramRecurrence',
		'AwarenessProgram'
	);
}