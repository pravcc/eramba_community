<?php
App::uses('CakeObject', 'Core');

App::uses('AppMigration', 'Lib');
class E101009 extends AppMigration {

/**
 * Should this migration update Database version in `settings` table on current DataSource connection.
 *
 * @var bool
 */
	public $updateVersion = true;

/**
 * Migration description. Used as a database version after successful migration if `$this->updateVersion` is true.
 *
 * @var string
 */
	public $description = 'e1.0.1.009';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'compliance_audit_provided_feedbacks' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'compliance_audit_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'user_id' => array('column' => 'user_id', 'unique' => 0),
						'compliance_audit_id' => array('column' => 'compliance_audit_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'create_field' => array(
				'compliance_audits' => array(
					'auditee_notifications' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'email_body'),
					'auditee_emails' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'auditee_notifications'),
					'auditor_notifications' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'auditee_emails'),
					'auditor_emails' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'auditor_notifications'),
					'show_analyze_title' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'auditor_emails'),
					'show_analyze_description' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'show_analyze_title'),
					'show_analyze_audit_criteria' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'show_analyze_description'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'compliance_audit_provided_feedbacks'
			),
			'drop_field' => array(
				'compliance_audits' => array('auditee_notifications', 'auditee_emails', 'auditor_notifications', 'auditor_emails', 'show_analyze_title', 'show_analyze_description', 'show_analyze_audit_criteria'),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		$ret = parent::before($direction);

		if ($direction == 'down') {
			$ret &= $this->manageDataDown();

			try {
				$ret &= $this->dropForeignKey(
					'compliance_audit_provided_feedbacks',
					null,
					'compliance_audit_provided_feedbacks_ib_fk_1'
				);

				$ret &= $this->dropForeignKey(
					'compliance_audit_provided_feedbacks',
					null,
					'compliance_audit_provided_feedbacks_ib_fk_2'
				);
			}
			catch (Exception $e) {
				return false;
			}
		}

		return $ret;
	}

	private function manageDataDown() {
		$ret = true;

		// insert

		return $ret;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		$ret = parent::after($direction);

		if ($direction == 'up') {
			try {
				$ret &= $this->addForeignKey(
					array('compliance_audit_provided_feedbacks', 'user_id'),
					array('users', 'id'),
					null,
					'compliance_audit_provided_feedbacks_ib_fk_1'
				);

				$ret &= $this->addForeignKey(
					array('compliance_audit_provided_feedbacks', 'compliance_audit_id'),
					array('compliance_audits', 'id'),
					null,
					'compliance_audit_provided_feedbacks_ib_fk_2'
				);

				// fix existing foreign key integrity to cascade cascade - applies to older versions too
				$ret &= $this->dropForeignKey(
					'awareness_program_missed_recurrences',
					null,
					'awareness_program_missed_recurrences_ibfk_2'
				);

				$ret &= $this->addForeignKey(
					array('awareness_program_missed_recurrences', 'awareness_program_recurrence_id'),
					array('awareness_program_recurrences', 'id'),
					null,
					'awareness_program_missed_recurrences_ibfk_2'
				);
			}
			catch (Exception $e) {
				return false;
			}

			if (!$ret) {
				return false;
			}

			$ret &= $this->manageDataUp();
		}
		
		return $ret;
	}

	private function manageDataUp() {
		$ret = true;

		// add custom field settings
		$ret &= $this->save('CustomFieldSetting', array(
			'model' => 'BusinessUnit',
			'status' => '0'
		), false, true);

		$ret &= $this->save('CustomFieldSetting', array(
			'model' => 'Process',
			'status' => '0'
		), false, true);

		$ret &= $this->save('CustomFieldSetting', array(
			'model' => 'ThirdParty',
			'status' => '0'
		), false, true);

		$ret &= $this->save('CustomFieldSetting', array(
			'model' => 'Asset',
			'status' => '0'
		), false, true);

		$ret &= $this->save('CustomFieldSetting', array(
			'model' => 'Risk',
			'status' => '0'
		), false, true);

		$ret &= $this->save('CustomFieldSetting', array(
			'model' => 'ThirdPartyRisk',
			'status' => '0'
		), false, true);

		$ret &= $this->save('CustomFieldSetting', array(
			'model' => 'BusinessContinuity',
			'status' => '0'
		), false, true);

		return $ret;
	}
}
