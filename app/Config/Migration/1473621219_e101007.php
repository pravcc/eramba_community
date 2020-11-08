<?php
App::uses('AppMigration', 'Lib');
class E101007 extends AppMigration {

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
	public $description = 'e1.0.1.007';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'awareness_program_active_users' => array(
					'email' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'uid'),
					'name' => array('type' => 'string', 'null' => false, 'length' => 155, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'email'),
				),
				'awareness_programs' => array(
					'text_file' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'questionnaire'),
					'text_file_extension' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'text_file'),
					'uploads_sort_json' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'text_file_extension'),
					'email_reminder_custom' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false, 'after' => 'email_body'),
					'email_reminder_subject' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'email_reminder_custom'),
					'email_reminder_body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'email_reminder_subject'),
					'active_users' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'after' => 'awareness_training_count'),
					'active_users_percentage' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false, 'after' => 'active_users'),
					'ignored_users' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'after' => 'active_users_percentage'),
					'ignored_users_percentage' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'unsigned' => false, 'after' => 'ignored_users'),
					'compliant_users' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'after' => 'ignored_users_percentage'),
					'compliant_users_percentage' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false, 'after' => 'compliant_users'),
					'not_compliant_users' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'after' => 'compliant_users_percentage'),
					'not_compliant_users_percentage' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false, 'after' => 'not_compliant_users'),
					'stats_update_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'unsigned' => false, 'after' => 'not_compliant_users_percentage'),
				),
				'awareness_reminders' => array(
					'email' => array('type' => 'string', 'null' => false, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'uid'),
					'reminder_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 2, 'unsigned' => false, 'after' => 'demo'),
				),
				'ldap_connectors' => array(
					'ldap_group_account_attribute' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'ldap_groupmemberlist_filter'),
					'ldap_group_fetch_email_type' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'ldap_group_account_attribute'),
					'ldap_group_email_attribute' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'ldap_group_fetch_email_type'),
					'ldap_group_mail_domain' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'ldap_group_email_attribute'),
				),
				'users' => array(
					'api_allow' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'unsigned' => false, 'after' => 'local_account'),
				),
			),
			'create_table' => array(
				'awareness_program_compliant_users' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'awareness_program_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'uid' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'awareness_program_id' => array('column' => 'awareness_program_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'awareness_program_not_compliant_users' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'awareness_program_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'uid' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'awareness_program_id' => array('column' => 'awareness_program_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'awareness_programs_security_policies' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'security_policy_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'awareness_program_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'security_policy_id' => array('column' => 'security_policy_id', 'unique' => 0),
						'awareness_program_id' => array('column' => 'awareness_program_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'alter_field' => array(
				'awareness_programs' => array(
					'questionnaire' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'log_security_policies' => array(
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
			),
			'drop_field' => array(
				'ldap_connectors' => array('ldap_groupmemberlist_name'),
			),
		),
		'down' => array(
			'drop_field' => array(
				'awareness_program_active_users' => array('email', 'name'),
				'awareness_programs' => array('text_file', 'text_file_extension', 'uploads_sort_json', 'email_reminder_custom', 'email_reminder_subject', 'email_reminder_body', 'active_users', 'active_users_percentage', 'ignored_users', 'ignored_users_percentage', 'compliant_users', 'compliant_users_percentage', 'not_compliant_users', 'not_compliant_users_percentage', 'stats_update_status'),
				'awareness_reminders' => array('email', 'reminder_type'),
				'ldap_connectors' => array('ldap_group_account_attribute', 'ldap_group_fetch_email_type', 'ldap_group_email_attribute', 'ldap_group_mail_domain'),
				'users' => array('api_allow'),
			),
			'drop_table' => array(
				'awareness_program_compliant_users', 'awareness_program_not_compliant_users', 'awareness_programs_security_policies'
			),
			'alter_field' => array(
				'awareness_programs' => array(
					'questionnaire' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'log_security_policies' => array(
					'description' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
			),
			'create_field' => array(
				'ldap_connectors' => array(
					'ldap_groupmemberlist_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
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
					'awareness_program_compliant_users',
					null,
					'awareness_program_compliant_users_ibfk_1'
				);

				$ret &= $this->dropForeignKey(
					'awareness_program_not_compliant_users',
					null,
					'awareness_program_not_compliant_users_ibfk_1'
				);

				$ret &= $this->dropForeignKey(
					'awareness_programs_security_policies',
					null,
					'awareness_programs_security_policies_ibfk_1'
				);

				$ret &= $this->dropForeignKey(
					'awareness_programs_security_policies',
					null,
					'awareness_programs_security_policies_ibfk_2'
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

		// blank space

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
					array('awareness_program_compliant_users', 'awareness_program_id'),
					array('awareness_programs', 'id'),
					null,
					'awareness_program_compliant_users_ibfk_1'
				);

				$ret &= $this->addForeignKey(
					array('awareness_program_not_compliant_users', 'awareness_program_id'),
					array('awareness_programs', 'id'),
					null,
					'awareness_program_not_compliant_users_ibfk_1'
				);

				$ret &= $this->addForeignKey(
					array('awareness_programs_security_policies', 'awareness_program_id'),
					array('awareness_programs', 'id'),
					null,
					'awareness_programs_security_policies_ibfk_1'
				);

				$ret &= $this->addForeignKey(
					array('awareness_programs_security_policies', 'security_policy_id'),
					array('security_policies', 'id'),
					null,
					'awareness_programs_security_policies_ibfk_2'
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

		// add cron index to settings
		$ret &= $this->save('SettingGroup', array(
			'slug' => 'CRON',
			'parent_slug' => 'ACCESSMGT',
			'name' => 'Cron Jobs',
			'url' => '{"controller":"cron","action":"index"}'
		), false, true);

		return $ret;
	}
}
