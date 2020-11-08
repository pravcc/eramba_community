<?php
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
App::uses('Debugger', 'Utility');
App::uses('CakeLog', 'Log');

class CacheCleaner {

/**
 * Ignored file names.
 * 
 * @var array
 */
	public static $ignoredFiles = [
		'empty',
		'.DS_Store'
	];

/**
 * External cache flders.
 * 
 * @var array
 */
	public static $externalFolders = [
		APP . 'Plugin/HtmlPurifier/Vendor/HtmlPurifier/library/HTMLPurifier/DefinitionCache/Serializer/'
	];

/**
 * Delete all cache files from folder.
 * 
 * @param string $folder Cache folder to clear.
 * @return boolean Success.
 */
	public static function deleteCache($folder = CACHE) {
		$folders = ($folder == CACHE) ? array_merge([$folder], static::$externalFolders) : $folder;

		$files = static::getFiles($folders);

		$ret = true;

		foreach ($files as $file) {
			$ret &= static::deleteFile($file);
		}

		return $ret;
	}

/**
 * Get list of files of input folders.
 * 
 * @param string|array $folders Folder paths.
 * @return array Files paths.
 */
	public static function getFiles($folders) {
		$folders = (array) $folders;
		$files = [];

		foreach ($folders as $folderPath) {
			$folder = new Folder($folderPath);
			$files = array_merge($files, $folder->tree()[1]);
		}

		return $files;
	}

/**
 * Delete file.
 * 
 * @param  string $filePath File path.
 * @return boolean Success.
 */
	public static function deleteFile($filePath) {
		$ret = true;
		$file = new File($filePath);

		if ($file->exists() && !static::ignoredFile($file->name())) {
			//check if file is not holded by another process
			$file->open('w');
			if (!flock($file->handle, LOCK_EX | LOCK_NB)) {
				flock($file->handle, LOCK_UN);
			}

			$ret = $file->delete();
		}

		//log on failure
		if (!$ret) {
			CakeLog::write('debug', sprintf('CacheCleaner - Can not delete cache file: %s', $file->path . "\n" . Debugger::trace()));
		}

		return $ret;
	}

/**
 * Check if file name is in ignore list.
 * 
 * @param string $fileName File name.
 * @return boolean 
 */
	public static function ignoredFile($fileName) {
		return in_array($fileName, static::$ignoredFiles);
	}
}