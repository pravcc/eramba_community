<?php
App::uses('AppMigration', 'Lib');
class E101012 extends AppMigration {

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
	public $description = 'e1.0.1.012';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
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

		$Workflow = $this->generateModel('Workflow');

		if ($direction == 'up') {
			$ret &= $Workflow->deleteAll(array(
				'model' => 'ComplianceFinding'
			));
		}
		else {
			$ret &= $this->save('Workflow', array(
				'id' => 30,
				'model' => 'ComplianceFinding',
				'name' => 'Compliance Findings',
				'notifications' => '0',
				'parent_id' => null
			), false, true);

			$ret &= $this->save('Workflow', array(
				'id' => 40,
				'model' => 'ComplianceAuditSetting',
				'name' => 'Compliance Audit Settings',
				'notifications' => '0',
				'parent_id' => '30'
			), false, true);
		}
		
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

		return $ret;
	}

}