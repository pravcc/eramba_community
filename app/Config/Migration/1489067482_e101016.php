<?php
App::uses('AppMigration', 'Lib');
App::uses('PhpReader', 'Configure');
App::uses('PhinxApp', 'Lib');

class E101016 extends AppMigration {

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
	public $description = 'e1.0.1.016';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(),
		'down' => array()
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		$ret = parent::before($direction);

		// need to load app version and db version currently stored in configure class, for the rest of the php process
		Configure::config('default', new PhpReader());
		$ret &= Configure::load('app', 'default', false);

		// start using new migrations and run it to update the database
		$PhinxApp = new PhinxApp();
		$ret &= $PhinxApp->getMigrate();

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
