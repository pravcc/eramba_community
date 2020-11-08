<?php
class BusinessContinuityTaskReminder extends AppModel {
	
	public $belongsTo = array(
		'BusinessContinuityTask',
		'User'
	);

}
