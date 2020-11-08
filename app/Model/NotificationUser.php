<?php
class NotificationUser extends AppModel {
	public $actsAs = array(
		'Containable'
	);

	public $hasAndBelongsToMany = array(
		'NotificationSystem'
	);
}
