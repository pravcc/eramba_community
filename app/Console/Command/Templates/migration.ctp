<?php
echo "<?php\n";
?>
App::uses('AppMigration', 'Lib');
class <?php echo $class; ?> extends AppMigration {

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
	public $description = '<?php echo $name; ?>';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
<?php echo $migration; ?>
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
