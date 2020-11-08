<?php
class NotificationSystemItemsScope extends AppModel {
	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'User'
	);
}
