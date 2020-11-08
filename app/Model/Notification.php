<?php
// this model manages the header notifications.
class Notification extends AppModel {
	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'User'
	);

	public function setNotification($data = array(), $options = array()) {
		$this->create();
		return $this->save($data, array('validate' => false, 'callbacks' => false, 'atomic' => true));
	}
}
