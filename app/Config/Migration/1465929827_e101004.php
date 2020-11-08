<?php
App::uses('AppMigration', 'Lib');
class E101004 extends AppMigration {

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
	public $description = 'e1.0.1.004';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'custom_field_options' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'custom_field_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'value' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 155, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'custom_field_id' => array('column' => 'custom_field_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'custom_field_settings' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'model' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 155, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 3, 'unsigned' => false),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'model' => array('column' => 'model', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'custom_field_values' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'model' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'foreign_key' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'custom_field_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'value' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'field_name' => array('column' => 'custom_field_id', 'unique' => 0),
						'model' => array('column' => 'model', 'unique' => 0),
						'foreign_key' => array('column' => 'foreign_key', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'custom_fields' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'custom_form_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'slug' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => false),
					'description' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'slug' => array('column' => 'slug', 'unique' => 1),
						'custom_form_id' => array('column' => 'custom_form_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'custom_forms' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'model' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 155, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 155, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'slug' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 155, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'slug' => array('column' => 'slug', 'unique' => 1),
						'model' => array('column' => 'model', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'custom_field_options', 'custom_field_settings', 'custom_field_values', 'custom_fields', 'custom_forms'
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
			try {
				$ret &= $this->dropForeignKey('custom_field_options', null, 'FK_custom_field_options_custom_fields');
				$ret &= $this->dropForeignKey('custom_field_values', null, 'FK_custom_field_values_custom_fields');
				$ret &= $this->dropForeignKey('custom_fields', null, 'FK_custom_fields_custom_forms');
			}
			catch (Exception $e) {
				return false;
			}
		}

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

		$ret = true;
		if ($direction == 'up') {
			try {
				$ret &= $this->addForeignKey(
					array('custom_field_options', 'custom_field_id'),
					array('custom_fields', 'id'),
					null,
					'FK_custom_field_options_custom_fields'
				);

				$ret &= $this->addForeignKey(
					array('custom_field_values', 'custom_field_id'),
					array('custom_fields', 'id'),
					null,
					'FK_custom_field_values_custom_fields'
				);

				$ret &= $this->addForeignKey(
					array('custom_fields', 'custom_form_id'),
					array('custom_forms', 'id'),
					null,
					'FK_custom_fields_custom_forms'
				);
			}
			catch (Exception $e) {
				return false;
			}

			if (!$ret) {
				return false;
			}

			$ret &= $this->insertData();
		}
		
		return $ret;
	}

	private function insertData() {
		$ret = true;

		// add custom field settings
		$ret &= $this->save('CustomFieldSetting', array(
			'model' => 'SecurityService',
			'status' => '0'
		), false, true);

		$ret &= $this->save('CustomFieldSetting', array(
			'model' => 'SecurityServiceAudit',
			'status' => '0'
		), false, true);

		$ret &= $this->save('CustomFieldSetting', array(
			'model' => 'SecurityServiceMaintenance',
			'status' => '0'
		), false, true);
		
		return $ret;
	}
}
