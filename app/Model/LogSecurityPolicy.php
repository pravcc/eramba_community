<?php
class LogSecurityPolicy extends AppModel {
	public $belongsTo = array(
		'SecurityPolicy',
		'UserEdit' => array(
			'className' => 'User',
			'foreignKey' => 'user_edit_id',
			'fields' => array('name', 'surname')
		)
	);
}
