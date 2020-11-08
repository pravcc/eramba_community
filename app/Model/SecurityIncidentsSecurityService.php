<?php
class SecurityIncidentsSecurityService extends AppModel {
	public $belongsTo = array(
		'SecurityIncident',
		'SecurityService' => array(
			'counterCache' => array(
				'security_incident_open_count' => array(
					'SecurityIncident.security_incident_status_id' => SECURITY_INCIDENT_ONGOING
				)
			)
		)
	);
}