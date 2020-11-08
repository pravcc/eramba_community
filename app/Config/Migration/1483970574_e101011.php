<?php
App::uses('AppMigration', 'Lib');
class E101011 extends AppMigration {

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
	public $description = 'e1.0.1.011';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'compliance_audits' => array(
					'show_findings' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'show_analyze_audit_criteria'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'compliance_audits' => array('show_findings'),
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
