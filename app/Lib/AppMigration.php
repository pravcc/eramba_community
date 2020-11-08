<?php
App::uses('CakeMigration', 'Migrations.Lib');
App::uses('ClassRegistry', 'Utility');

/**
 * Extended base class for handling App database migrations. Added support for managing foreign key constraints.
 *
 * @deprecated In favour of new Phinx migrations.
 */
class AppMigration extends CakeMigration {

/**
 * Should this migration update Database version in `settings` table on current DataSource connection.
 *
 * @var bool
 */
	public $updateVersion = true;

	public function __construct($options = array()) {
		parent::__construct($options);

		//dynamic model for DataSource usage.
		$this->Model = $this->generateModel('Setting');
	}

	protected function initModel($name) {
		$m = ClassRegistry::init(array('class' => $name, 'alias' => $name, 'ds' => $this->connection));
		$m->setDataSource($this->connection);

		return $m;
	}

	protected function q($sql) {
		return checkQueryResponse($this->Model->query($sql));
	}

	/**
	 * Before migration callback stops downgrades if not in debug mode.
	 *
	 * @param string $direction Direction of migration process (up or down)
	 * @return bool Should process continue
	 */
	public function before($direction) {
		if ($direction === 'down' && !Configure::read('debug')) {
			throw new InternalErrorException(__('Downgrade not possible. Why would you want that anyways?'));
		}

		return true;
	}

	/**
	 * After migration callback
	 *
	 * @param string $direction Direction of migration process (up or down)
	 * @return bool Should process continue
	 */
	public function after($direction) {
		$ret = $this->bumpDbVersion();

		return $ret;
	}

	/**
	 * Update DB version after migration.
	 */
	private function bumpDbVersion() {
		if (empty($this->updateVersion)) {
			return true;
		}

		$Setting = $this->initModel('Setting');
		$db = $Setting->getVariable('DB_SCHEMA_VERSION');

		// handle customized db version suffix
		$arr = explode('-', $db);

		$newVersion = array($this->description);
		if (isset($arr[1]) && !empty($arr[1])) {
			$newVersion[] = $arr[1];
		}

		return $Setting->updateVariable('DB_SCHEMA_VERSION', implode('-', $newVersion));
	}

	/**
	 * Executes query "ALTER TABLE `table_name` ADD CONSTRAINT `FK_table_name_ref_table_name` FOREIGN KEY (`column_name`) REFERENCES `ref_table_name` (`ref_column_name`) ON DELETE
	 * SET NULL ON UPDATE CASCADE;"
	 *
	 * @param array $tableArr Which table and column to be altered. Formatted array('table_name', 'column_name').
	 * @param array $refTableArr Reference table and column for association. Formatted array('ref_table_name', 'ref_column_name').
	 * @param array $integrity Constraint integrity array. Formatted array('delete' => 'TYPE', 'update' => 'TYPE').
	 * @param mixed  $customConstraint If constraint name for the foreign key does not follow FK_$table_$refTable naming convention, define the name here.
	 * @return bool Execution results for ALTER TABLE query.
	 */
	protected function addForeignKey($tableArr, $refTableArr, $integrity = null, $customConstraint = null) {
		$table = $tableArr[0];
		$foreignKey = $tableArr[1];

		$refTable = $refTableArr[0];
		$refCol = $refTableArr[1];

		if (empty($integrity)) {
			$integrity = array('delete' => 'CASCADE', 'update' => 'CASCADE');
		}

		$integrity = $this->getIntegrityValue($integrity);

		if (!empty($customConstraint)) {
			$constraint = $customConstraint;
		}
		else {
			$constraint = $this->getConstraint($tableArr[0], $refTableArr[0]);
		}

		$sql = "ALTER TABLE `{$table}` ADD CONSTRAINT `{$constraint}` FOREIGN KEY (`{$foreignKey}`) REFERENCES `{$refTable}` (`{$refCol}`) {$integrity};";
		return $this->q($sql);
	}

	/**
	* Executes query "ALTER TABLE `qqqqq` DROP FOREIGN KEY `FK_qqqqq_users`;".
	*
	* @param string $table Table name where to execute ALTER query at.
	* @param mixed  $refTable Reference table name where foreign key points.
	* @param mixed  $customConstraint If constraint name for the foreign key does not follow FK_$table_$refTable naming convention, define the name here.
	* @return bool Execution results for ALTER TABLE query.
	*/
	protected function removeForeignKey($table, $refTable = null, $customConstraint = null) {
		if (!empty($customConstraint)) {
			$constraint = $customConstraint;
		}
		else {
			$constraint = $this->getConstraint($table, $refTable);
		}

		$sql = "ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`;";
		return $this->q($sql);
	}

	/**
	 * Alias for removeForeignKey() method.
	 */
	protected function dropForeignKey($table, $refTable = null, $customConstraint = null) {
		return $this->removeForeignKey($table, $refTable, $customConstraint);
	}

	/**
	 * Get foreign key constraint integrity string for usage in query.
	 *
	 * @param array $integrity Where 'delete' and 'update' keys are defined.
	 * @return string Foreign key integrity value.
	 */
	protected function getIntegrityValue($integrity) {
		return "ON DELETE " . $integrity['delete'] . " ON UPDATE " . $integrity['update'];
	}

	/**
	 * Generate convention constraint name from used tables.
	 *
	 * @return string Constraint name.
	 */
	protected function getConstraint($table, $refTable) {
		$c = 'FK_' . $table . '_' . $refTable;

		// constraint can have max 64 chars
		if (strlen($c) >= 64) {
			$c = substr($c, 0, 63);
		}

		return $c;
	}

	/**
	 * Wrapper fn to insert data into table.
	 *
	 * @param bool $checkExists Check if the data already exists.
	 */
	protected function save($model, $data, $validate = false, $checkExists = false) {
		$m = $this->generateModel($model);

		if ($checkExists) {
			$count = $m->find('count', array(
				'conditions' => $data,
				'recursive' => -1
			));

			if ($count) {
				return true;
			}
		}

		$m->create();
		$m->set($data);
		return $m->save(null, $validate);
	}
	

}