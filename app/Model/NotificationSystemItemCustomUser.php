<?php
class NotificationSystemItemCustomUser extends AppModel {
	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'User',
		'NotificationObject' => array(
			'foreignKey' => 'notification_system_item_object_id'
		)
	);
}
