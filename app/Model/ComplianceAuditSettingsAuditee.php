<?php
class ComplianceAuditSettingsAuditee extends AppModel {
	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'Auditee' => array(
			'className' => 'User',
			'foreignKey' => 'auditee_id',
			// 'fields' => array('id', 'name', 'surname')
		),
		'ComplianceAuditSetting',
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
	);

	public $hasMany = array(
	);
}
