<?php
class BusinessUnitsUser extends AppModel {
	public $actsAs = array(
		'Containable'
	);
	
	public $belongsTo = array(
		'User'
	);
}
