<?php
App::uses('Model', 'Model');
App::uses('File', 'Utility');
App::uses('CakeLog', 'Log');
App::uses('CakeNumber', 'Utility');

/**
 * This is a standalone class that interacts with blank Model class instance
 * and makes a backup of a database by directly querying the database.
 */
class BackupDatabaseLib
{
	public $ds = 'default';

	protected function _blankModel()
	{
		$modelConfig = ['table' => 'settings', 'name' => 'BootstrapSetting', 'ds' => $this->ds];

		return (new Model($modelConfig));
	}

	/**
	 * Build the database backup.
	 * 
	 * @return string The whole sql string.
	 */
	public function build($path = null, $dropTables = false)
	{
		$ret = true;

		$Model = $this->_blankModel();

		$ds = $Model->getDataSource($Model->useDbConfig);
		$database = $ds->config['database'];
		$file = new File($path);

		if ($file->exists()) {
			$ret &= $file->delete();
		}

		// create blank output file
		$ret &= $file->safe() && $file->create();

		if (!$ret) {
			return false;
		}

		//exclude queue table from exporting values because of MEDIUM_TEXT data column
		$excludedTableInserts = array('queue');

		// get all table names in the database so we backup everything not depending on the app version
		$result = $Model->query("SHOW TABLES");
		$tables = array();

		foreach ( $result as $table ) {
			$tables[] = current(current($table));
		}

		$return = "SET FOREIGN_KEY_CHECKS=0;\n\n";
		foreach ( $tables as $table ) {
			if ($dropTables) {
				$return .= 'DROP TABLE IF EXISTS `' . $table . '`;';
			}
			else {
				$return .= "TRUNCATE `" . $table . "`;\n\n";
			}
			
			// create table statement
			$create_table = $Model->query("SHOW CREATE TABLE `" . $table . "`");

			if ($dropTables) {
				$return .= "\n\n" . $create_table[0][0]['Create Table'] . ";\n\n";
			}

			// skip one of the excluded tables defined statically
			if (in_array($table, $excludedTableInserts)) {
				continue;
			}

			// for performance reasons we consider splitting SELECT statement by size of each table
			$status = $Model->query("SHOW TABLE STATUS FROM  `{$database}` LIKE '" . $table ."'");
			$tableSize = $status[0]['TABLES']['Data_length'];

			// iterate each 4.77mb of data from each table
			$splitRows = ceil($tableSize/5000000);

			// in case no splitting is required, skip additional calculations
			$selectIterate = $limit = null;
			if ($splitRows != 1) {
				$count = $Model->query("SELECT COUNT(*) FROM " . $table);
				$count = $count[0][0]['COUNT(*)'];
				$selectIterate = ceil($count / $splitRows);
			}

			$result = [];
			for ($i=0; $i < $splitRows; $i++) {
				// builds OFFSET part of the LIMIT part in the SELECT query
				$key = '';
				if ($i > 0) {
					$key = ($i * $selectIterate);
					$key = $key  . ',';
				}

				// builds LIMIT part using previously calculated OFFSET
				$limit = '';
				if ($selectIterate !== null) {
					// e.g LIMIT 15, 30
					$limit = "LIMIT {$key} {$selectIterate}";
				}
				
				// pull requested data
				$nextIterationData = $Model->query("SELECT * FROM `" . $table . "` {$limit}");

				// in case there is no data to be inserted for this certain table, we skip putting an INSERT statement
				if (empty($nextIterationData)) {
					continue;
				}

				// builds INSERT statement(s) depending on calculations
				$return .= 'INSERT INTO `' . $table . '` VALUES ';
				$lengthRow = count($nextIterationData);
				$iRow = 0;
				foreach ( $nextIterationData as $row ) {
					$return .= "(";

					$lengthCol = count( $row[ $table ] );
					$iCol = 0;
					foreach ( $row[ $table ] as $value ) {
						$return .= $ds->value($value);
						
						if ( $iCol < $lengthCol - 1 ) {
							$return .= ',';
						}

						$iCol++;
					}
					$iRow++;

					if ( $iRow < $lengthRow ) {
						$return .= "),\n";
					}
					else {
						 $return .= ");\n";
					}
				}

				$ret &= $file->append($return);
				$return = '';
			}
		}

		// process TRIGGERS
		$triggers = $Model->query("SHOW TRIGGERS");
		$triggerSyntax = '';
		foreach ($triggers as $trigger) {
			$trigger = $Model->query("SHOW CREATE TRIGGER `" . $trigger['TRIGGERS']['Trigger'] . "`");
			$originalStatement = $trigger[0][0]['SQL Original Statement'];

			// definer reset because the clean schema for reset database used to force root@localhost
			// as definer which was not ok
			$originalStatement = str_replace('DEFINER=`root`@`localhost`', 'DEFINER=CURRENT_USER()', $originalStatement);

			$triggerSyntax .= "\n\n" . $originalStatement . ";\n\n";
		}

		$return .= $triggerSyntax;

		$return .= "SET FOREIGN_KEY_CHECKS=1;";

		$ret &= $file->append($return) && $file->close();
		$return = '';

		// lets log unusually high memory usage, more than 500mb
		if ($file->size() >= 500000000) {
			$size = CakeNumber::toReadableSize($file->size());
			CakeLog::write(LOG_DEBUG, sprintf('Database Backup file too huge - %s', $size));
		}

		return $ret;
	}
}
