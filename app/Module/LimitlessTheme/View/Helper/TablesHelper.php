<?php
App::uses('AppHelper', 'View/Helper');
App::uses('LimitlessThemeException', 'Module/LimitlessTheme/Error');
App::uses('TablesTrait', 'Module/LimitlessTheme/Lib/Tables/Trait');
App::uses('Table', 'Module/LimitlessTheme/Lib/Tables');

class TablesHelper extends AppHelper
{
	use TablesTrait;

	public $helpers = array('Html', 'Form');

	public function __construct(View $view, $settings = array())
	{
		parent::__construct($view, $settings);
	}

	/**
	 * Data Table Objects
	 */
	protected $tables;

	/**
	 * Name of the table which is currently used as default (for easy access to table through this helper)
	 */
	protected $currentTableName = '';

	public function setCurrentTable($name)
	{
		if (!isset($this->tables[$name])) {
			return false;
		}

		$this->currentTableName = $name;
		return true;
	}

	/**
	 * Create new Table object
	 * @param  array      $options   Set values to params of the class
	 * @return Table  Returns Table object
	 */
	public function createTable(array $options = [])
	{
		$table = $this->createObject('Table', 'tables', $options);
		$this->currentTableName = $table->getName();

		return $table;
	}

	/**
	 * Get Table instance
	 * @param  string     $name                        Name of Table (index by which user can reach the table object)
	 * @param  array      $options                     Options for createTable method of this helper
	 * @param  int        $createNew                   Options are:
	 *                                                 	- 0 Do not create new table
	 *                                                 	- 1 (default) Create new table if requested or current table not exists
	 *                                                 	- 2 Force to create new table
	 * @return Table                               Returns Table object
	 */
	public function table($name = null, array $options = [], int $createNewTable = 1)
	{
		$table = null;
		if ($createNewTable < 2) {
			if (empty($name)) {
				if (isset($this->tables[$this->currentTableName])) {
					$table = $this->tables[$this->currentTableName];
				}
			} elseif (isset($this->tables[$name])) {
				$table = $this->tables[$name];
			}
		}

		if (empty($table) && $createNewTable > 0) {
			$table = $this->createTable($options);
		}

		if (empty($table)) {
			throw new LimitlessThemeException(__('The table (%s) you\'re trying to use doesn\'t exists', $name));
		}

		return $table;
	}
}
