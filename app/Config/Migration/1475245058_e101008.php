<?php
App::uses('AppMigration', 'Lib');
class E101008 extends AppMigration {

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
	public $description = 'e1.0.1.008';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'advanced_filter_cron_result_items' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'advanced_filter_cron_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'data' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'advanced_filter_cron_id' => array('column' => 'advanced_filter_cron_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'advanced_filter_crons' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'advanced_filter_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'cron_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false),
					'result' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'execution_time' => array('type' => 'float', 'null' => false, 'default' => null, 'unsigned' => false),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'advanced_filter_id' => array('column' => 'advanced_filter_id', 'unique' => 0),
						'cron_id' => array('column' => 'cron_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'create_field' => array(
				'advanced_filters' => array(
					'log_result_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 2, 'unsigned' => false, 'after' => 'model'),
					'log_result_data' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 2, 'unsigned' => false, 'after' => 'log_result_count'),
					'deleted' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2, 'unsigned' => false, 'after' => 'log_result_data'),
					'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => null, 'after' => 'deleted'),
				),
				'notification_system_items' => array(
					'report_send_empty_results' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 2, 'unsigned' => true, 'after' => 'email_body'),
					'report_attachment_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 2, 'unsigned' => true, 'after' => 'report_send_empty_results'),
					'advanced_filter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'after' => 'report_attachment_type'),
					'indexes' => array(
						'advanced_filter_id' => array('column' => 'advanced_filter_id', 'unique' => 0),
					),
				),
			),
			'alter_field' => array(
				'notification_system_items_objects' => array(
					'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
				),
				'notification_system_items' => array(
					'type' => array('type' => 'string', 'null' => false, 'length' => 45, 'collate' => 'utf8_general_ci', 'charset' => 'utf8')
				)
			),
		),
		'down' => array(
			'drop_table' => array(
				'advanced_filter_cron_result_items', 'advanced_filter_crons'
			),
			'drop_field' => array(
				'advanced_filters' => array('log_result_count', 'log_result_data', 'deleted', 'deleted_date'),
				'notification_system_items' => array('report_send_empty_results', 'report_attachment_type', 'advanced_filter_id', 'indexes' => array('advanced_filter_id')),
			),
			'alter_field' => array(
				'notification_system_items_objects' => array(
					'foreign_key' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
				),
				'notification_system_items' => array(
					'type' => array('type' => 'string', 'null' => false, 'length' => 45, 'collate' => 'utf8_general_ci', 'charset' => 'utf8')
				)
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
					'advanced_filter_cron_result_items',
					null,
					'advanced_filter_cron_result_items_ibfk_1'
				);

				$ret &= $this->dropForeignKey(
					'advanced_filter_crons',
					null,
					'advanced_filter_cron_ibfk_1'
				);

				$ret &= $this->dropForeignKey(
					'advanced_filter_crons',
					null,
					'advanced_filter_cron_ibfk_2'
				);

				$ret &= $this->dropForeignKey(
					'notification_system_items',
					null,
					'notification_system_items_ibfk_1'
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
					array('advanced_filter_cron_result_items', 'advanced_filter_cron_id'),
					array('advanced_filter_crons', 'id'),
					null,
					'advanced_filter_cron_result_items_ibfk_1'
				);

				$ret &= $this->addForeignKey(
					array('advanced_filter_crons', 'advanced_filter_id'),
					array('advanced_filters', 'id'),
					null,
					'advanced_filter_cron_ibfk_1'
				);

				$ret &= $this->addForeignKey(
					array('advanced_filter_crons', 'cron_id'),
					array('cron', 'id'),
					array('update' => 'CASCADE', 'delete' => 'RESTRICT'),
					'advanced_filter_cron_ibfk_2'
				);

				$ret &= $this->addForeignKey(
					array('notification_system_items', 'advanced_filter_id'),
					array('advanced_filters', 'id'),
					array('update' => 'CASCADE', 'delete' => 'RESTRICT'),
					'notification_system_items_ibfk_1'
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

		return $ret;
	}
}
