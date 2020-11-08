<?php
App::uses('AppMigration', 'Lib');
class E101006 extends AppMigration {

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
	public $description = 'e1.0.1.006';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'compliance_audits' => array(
					'status' => array('type' => 'string', 'null' => false, 'default' => 'started', 'length' => 50, 'collate' => 'utf8_general_ci', 'comment' => 'started or stopped', 'charset' => 'utf8', 'after' => 'email_body'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'compliance_audits' => array('status'),
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
		
		return $ret;
	}
}
