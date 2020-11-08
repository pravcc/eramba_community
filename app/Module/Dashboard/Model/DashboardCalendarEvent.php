<?php
App::uses('DashboardAppModel', 'Dashboard.Model');

class DashboardCalendarEvent extends DashboardAppModel {
	public $useTable = 'calendar_events';

	public $actsAs = [
		'AuditLog.Auditable' => [
			'ignore' => [
				'created'
			]
		],
		'Acl' => [
			'type' => 'controlled'
		],
		'CustomRoles.CustomRoles',
		'AppNotification.AppNotification'
	];

	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
	}

	public function afterSave($created, $options = array())
	{
		// Cache::clearGroup('Visualisation', 'visualisation');
	}

	public function afterDelete()
	{
		// Cache::clearGroup('Visualisation', 'visualisation');
	}

	public function parentNode($type) {

		if (empty($this->data)) {
			return null;
		}

		return [
			$this->data[$this->alias]['model'] => [
				'id' => $this->data[$this->alias]['foreign_key']
			]
		];
	}
	
}
