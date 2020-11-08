<?php
App::uses('BackupRestoreAppModel', 'BackupRestore.Model');
App::uses('BackupDatabaseLib', 'BackupRestore.Lib');
App::uses('DebugMemory', 'DebugKit.Lib');

class BackupRestore extends BackupRestoreAppModel {
	public $useTable = false;

	public $actsAs = array(
		'Uploader.FileValidation' => array(
			'ZipFile' => array(
				'extension' => array('zip'),
				'mimeType' => array('application/x-zip-compressed', 'application/zip-compressed', 'application/zip'),
				'required' => array(
					'rule' => array('required'),
					'message' => 'File required',
				)
			)
		)
	);

	/**
	 * Makes a backup of current database.
	 */
	public function backupDatabase($path = null, $dropTables = false) {
		$BackupDatabaseLib = new BackupDatabaseLib();

		return $BackupDatabaseLib->build($path, $dropTables);
	}

	/**
	 * adding backup files to zip archive
	 */
	public function zipBackupFiles($archivePath, $files) {
		$zip = new ZipArchive();
		if (!$zip->open($archivePath, ZipArchive::CREATE)) {
			return false;
			
		}

		foreach ($files as $archiveFileName => $file) {
			if (!$zip->addFile($file, $archiveFileName)) {
				return false;
			}
		}
		
		$zip->close();

		return true;
	}

	/**
	 * Makes a sql query to restore database.
	 *
	 * @deprecated
	 */
	public function restoreDatabase( $path = null ) {
		// Temporary variable, used to store current query
		$templine = '';
		// Read in entire file
		$lines = file( $path );

		if ( ! $lines ) {
			return false;
		}

		foreach ( $lines as $line ) {
			if ( substr( $line, 0, 2 ) == '--' || $line == '' ) {
				continue;
			}

			$templine .= $line;
			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($line), -1, 1) == ';') {
				//debug($templine);
				 // Perform the query
				$this->query($templine);// or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
				// Reset temp variable to empty
				$templine = '';
			}
		}

		return true;
	}
}