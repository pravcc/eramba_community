<?php
class NotificationEmail extends AppModel {
	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'NotificationSystem'
	);
}
