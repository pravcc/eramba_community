<?php
App::uses('AppModule', 'Lib');
App::uses('AppMigration', 'Lib');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeLog', 'Log');

class E101010 extends AppMigration {

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
	public $description = 'e1.0.1.010';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'advanced_filter_user_settings' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'advanced_filter_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'default_index' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'unsigned' => false),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'advanced_filter_id' => array('column' => 'advanced_filter_id', 'unique' => 0),
						'user_id' => array('column' => 'user_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'audit_deltas' => array(
					'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'audit_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'property_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'old_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'new_value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'audit_id' => array('column' => 'audit_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'audits' => array(
					'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'version' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'event' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'model' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'entity_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'request_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'json_object' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'source_id' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'restore_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'restore_id' => array('column' => 'restore_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'backups' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'sql_file' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'deleted_files' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'unsigned' => false),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'bulk_action_objects' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'bulk_action_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'model' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'foreign_key' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'bulk_action_objects_ibfk_1' => array('column' => 'bulk_action_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'bulk_actions' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 2, 'unsigned' => false),
					'model' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'json_data' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'idx_user_id' => array('column' => 'user_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'queue' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'data' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'queue_id' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'unsigned' => false),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'create_field' => array(
				'advanced_filters' => array(
					'private' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'unsigned' => false, 'after' => 'model'),
				),
				'compliance_audit_settings' => array(
					'deleted' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'unsigned' => false, 'after' => 'modified'),
					'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => null, 'after' => 'deleted'),
				),
				'compliance_audits' => array(
					'deleted' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'unsigned' => false, 'after' => 'modified'),
					'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => null, 'after' => 'deleted'),
				),
				'compliance_findings' => array(
					'deleted' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'unsigned' => false, 'after' => 'modified'),
					'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => null, 'after' => 'deleted'),
				),
				'reviews' => array(
					'version' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'completed'),
				),
			),
			'drop_field' => array(
				'compliance_audit_settings' => array('auditee_notifications', 'auditee_emails', 'auditor_notifications', 'auditor_emails', 'workflow_status', 'workflow_owner_id', 'indexes' => array('workflow_owner_id')),
				'compliance_audit_settings_auditees' => array('created'),
				'compliance_findings' => array('workflow_owner_id', 'workflow_status', 'indexes' => array('workflow_owner_id')),
			),
			'alter_field' => array(
				'compliance_findings' => array(
					'deadline' => array('type' => 'date', 'null' => true, 'default' => null),
				),
				'business_units' => array(
					'name' => array('type' => 'string', 'null' => false, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'security_policies' => array(
					'url' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
				'third_party_types' => array(
					'name' => array('type' => 'string', 'null' => false, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'advanced_filter_user_settings', 'audit_deltas', 'audits', 'backups', 'bulk_action_objects', 'bulk_actions', 'queue'
			),
			'drop_field' => array(
				'advanced_filters' => array('private'),
				'compliance_audit_settings' => array('deleted', 'deleted_date'),
				'compliance_audits' => array('deleted', 'deleted_date'),
				'compliance_findings' => array('deleted', 'deleted_date'),
				'reviews' => array('version'),
			),
			'create_field' => array(
				'compliance_audit_settings' => array(
					'auditee_notifications' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false),
					'auditee_emails' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false),
					'auditor_notifications' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false),
					'auditor_emails' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false),
					'workflow_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false),
					'workflow_owner_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'indexes' => array(
						'workflow_owner_id' => array('column' => 'workflow_owner_id', 'unique' => 0),
					),
				),
				'compliance_audit_settings_auditees' => array(
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
				),
				'compliance_findings' => array(
					'workflow_owner_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'workflow_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false),
					'indexes' => array(
						'workflow_owner_id' => array('column' => 'workflow_owner_id', 'unique' => 0),
					),
				),
			),
			'alter_field' => array(
				'compliance_findings' => array(
					'deadline' => array('type' => 'date', 'null' => false, 'default' => null),
				),
				'security_policies' => array(
					'url' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
			),
		),
	);


/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		$ret = parent::after($direction);

		if ($direction == 'down') {
			try {
				//workflow
				$ret &= $this->addForeignKey(
					array('compliance_findings', 'workflow_owner_id'),
					array('users', 'id'),
					null,
					'compliance_findings_ibfk_4'
				);

				$ret &= $this->addForeignKey(
					array('compliance_audit_settings', 'workflow_owner_id'),
					array('users', 'id'),
					null,
					'compliance_audit_settings_ibfk_3'
				);
			}
			catch (Exception $e) {
				return false;
			}
		}

		if ($direction == 'up') {
			try {
				$ret &= $this->q("ALTER TABLE `queue` CHANGE COLUMN `data` `data` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL  COMMENT '' AFTER `id`;");

				$ret &= $this->addForeignKey(
					array('advanced_filter_user_settings', 'advanced_filter_id'),
					array('advanced_filters', 'id'),
					null,
					'advanced_filter_user_settings_ib_fk_1'
				);

				$ret &= $this->addForeignKey(
					array('advanced_filter_user_settings', 'user_id'),
					array('users', 'id'),
					null,
					'advanced_filter_user_settings_ib_fk_2'
				);

				//audit_deltas
				$ret &= $this->addForeignKey(
					array('audit_deltas', 'audit_id'),
					array('audits', 'id'),
					null,
					'audit_deltas_ibfk_1'
				);

				//audits
				$ret &= $this->addForeignKey(
					array('audits', 'restore_id'),
					array('audits', 'id'),
					array('update' => 'CASCADE', 'delete' => 'SET NULL'),
					'audits_ibfk_1'
				);

				//bulk_action_objects
				$ret &= $this->addForeignKey(
					array('bulk_action_objects', 'bulk_action_id'),
					array('bulk_actions', 'id'),
					null,
					'bulk_action_objects_ibfk_1'
				);

				//bulk_actions
				$ret &= $this->addForeignKey(
					array('bulk_actions', 'user_id'),
					array('users', 'id'),
					array('update' => 'CASCADE', 'delete' => 'SET NULL'),
					'bulk_actions_ibfk_1'
				);
			}
			catch (Exception $e) {
				return false;
			}

			if (!$ret) {
				return false;
			}

			$ret &= $this->manageDataUp();

			if ($ret) {
				if (/*$ret &= */ !$this->removeOlDFiles()) {
					//we just log it
					
					$__msg = 'Failed to remove old files from the application during an update to version e1.0.6.021.';
					CakeLog::write('updates', $__msg);
					CakeLog::write('error', $__msg);
				}
			}
		}

		AppModule::loadAll();

		return $ret;
	}

	private function removeOlDFiles() {
		$files = array(
			'Locale/acl.pot',
			'Locale/cake.pot',
			'Locale/cake_console.pot',
			'Locale/cake_dev.pot',
			'Locale/debug_kit.pot',
			'Locale/default.pot',
			'Locale/fra/LC_MESSAGES/default.po',
			'Locale/html_purifier.pot',
			'Locale/kor/LC_MESSAGES/default.po',
			'Locale/kor/LC_MESSAGES/korean.po',
			'Locale/nld/LC_MESSAGES/default.po',
			'Locale/por/LC_MESSAGES/default.po',
			'Locale/search.pot',
			'Locale/spa/LC_MESSAGES/default.po',
			'Controller/BackupRestoreController.php',
			'Model/BackupRestore.php',
			'Test/Case/Model/BackupRestoreTest.php',
			'View/BackupRestore/index.ctp'
		);

		$writable = true;
		$objects = array();
		foreach ($files as $key => $value) {
			$object = new File(ROOT . DS . 'app' . DS . $value);
			$writable &= $object->writable();

			$objects[] = $object;
		}

		if (empty($writable)) {
			return false;
		}

		$ret = true;
		if ($writable) {
			foreach ($objects as $object) {
				$ret &= $object->delete();
			}
		}

		return $ret;
	}

	private function manageDataUp() {
		$ret = true;

		// date field invalid value to null
		$ComplianceFinding = $this->generateModel('ComplianceFinding');
		$ret &= $ComplianceFinding->updateAll(
			array(
				'ComplianceFinding.deadline' => NULL
			),
			array('ComplianceFinding.deadline' => '0000-00-00')
		);

		// add backup to settings
		$ret &= $this->save('SettingGroup', array(
			'slug' => 'BACKUP',
			'parent_slug' => 'DB',
			'name' => 'Backup Configuration',
			'url' => null,
			'order' => 2
		), false, true);

		$ret &= $this->save('Setting', array(
			'name' => 'Backups Enabled',
			'variable' => 'BACKUPS_ENABLED',
			'value' => 0,
			'type' => 'checkbox',
			'setting_group_slug' => 'BACKUP',
			'setting_type' => 'constant'
		), false, true);

		$ret &= $this->save('Setting', array(
			'name' => 'Backup Day Period',
			'variable' => 'BACKUP_DAY_PERIOD',
			'value' => 1,
			'type' => 'select',
			'options' => '{"1":"Every day","2":"Every 2 days","3":"Every 3 days","4":"Every 4 days","5":"Every 5 days","6":"Every 6 days","7":"Every 7 days"}',
			'setting_group_slug' => 'BACKUP',
			'setting_type' => 'constant'
		), false, true);

		$ret &= $this->save('Setting', array(
			'name' => 'Backup Files Limit',
			'variable' => 'BACKUP_FILES_LIMIT',
			'value' => 15,
			'type' => 'select',
			'options' => '{"1":"1","5":"5","10":"10","15":"15"}',
			'setting_group_slug' => 'BACKUP',
			'setting_type' => 'constant'
		), false, true);

		$ret &= $this->save('Setting', array(
			'name' => 'Name',
			'variable' => 'EMAIL_NAME',
			'value' => '',
			'type' => 'text',
			'setting_group_slug' => 'MAILCNF',
			'setting_type' => 'constant',
			'order' => 6
		), false, true);

		$SettingGroup = $this->generateModel('SettingGroup');

		$ds = $SettingGroup->getDataSource();
		$url = $ds->value('{"controller":"backupRestore","action":"index", "plugin":"backupRestore"}', 'string');
		$ret &= $SettingGroup->updateAll(
			array(
				'SettingGroup.url' => $url
			),
			array('SettingGroup.slug' => 'BAR')
		);

		$Setting = $this->generateModel('Setting');
		$opts = $ds->value('{"0":"No Encryption","1":"SSL","2":"TLS"}', 'string');
		$ret &= $Setting->updateAll(
			array(
				'Setting.name' => "'Encryption'",
				'Setting.type' => "'select'",
				'Setting.options' => $opts
			),
			array('Setting.variable' => 'USE_SSL')
		);

		return $ret;
	}


/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		$ret = parent::before($direction);

		if ($direction == 'up') {
			try {
				//workflow
				$ret &= $this->dropForeignKey(
					'compliance_audit_settings',
					null,
					'compliance_audit_settings_ibfk_3'
				);

				$ret &= $this->dropForeignKey(
					'compliance_findings',
					null,
					'compliance_findings_ibfk_4'
				);
			}
			catch (Exception $e) {
				return false;
			}
		}

		if ($direction == 'down') {
			$ret &= $this->manageDataDown();

			try {
				$ret &= $this->dropForeignKey(
					'advanced_filter_user_settings',
					null,
					'advanced_filter_user_settings_ib_fk_1'
				);

				$ret &= $this->dropForeignKey(
					'advanced_filter_user_settings',
					null,
					'advanced_filter_user_settings_ib_fk_2'
				);

				//audit_deltas
				$ret &= $this->dropForeignKey(
					'audit_deltas',
					null,
					'audit_deltas_ibfk_1'
				);

				//audits
				$ret &= $this->dropForeignKey(
					'audits',
					null,
					'audits_ibfk_1'
				);

				//bulk_action_objects
				$ret &= $this->dropForeignKey(
					'bulk_action_objects',
					null,
					'bulk_action_objects_ibfk_1'
				);

				//bulk_actions
				$ret &= $this->dropForeignKey(
					'bulk_actions',
					null,
					'bulk_actions_ibfk_1'
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

		// date field rollback from null to invalid
		$ComplianceFinding = $this->generateModel('ComplianceFinding');
		$ret &= $ComplianceFinding->updateAll(
			array(
				'ComplianceFinding.deadline' => "'0000-00-00'"
			),
			array('ComplianceFinding.deadline' => NULL)
		);

		$SettingGroup = $this->generateModel('SettingGroup');
		$ret &= $SettingGroup->deleteAll(array(
			'slug' => 'BACKUP'
		));

		$ds = $SettingGroup->getDataSource();
		$url = $ds->value('{"controller":"backupRestore","action":"index"}', 'string');
		$ret &= $SettingGroup->updateAll(
			array(
				'SettingGroup.url' => $url
			),
			array('SettingGroup.slug' => 'BAR')
		);

		$Setting = $this->generateModel('Setting');
		$ret &= $Setting->deleteAll(array(
			'variable' => 'EMAIL_NAME'
		));

		$ret &= $Setting->updateAll(
			array(
				'Setting.name' => "'Use SSL'",
				'Setting.type' => "'checkbox'",
				'Setting.options' => "'NULL'"
			),
			array('Setting.variable' => 'USE_SSL')
		);

		return $ret;
	}

}
