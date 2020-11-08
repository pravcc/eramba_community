<?php
define('CACHE_FOLDER', '..' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);

$success = true; 

$folders = ['.', '*'];
$files = glob(CACHE_FOLDER . '{' . implode(',', $folders) . '}' . DIRECTORY_SEPARATOR . '*', GLOB_BRACE);

if(!empty($files)){
	foreach ($files as $file) {
		if (is_file($file) && basename($file) !== 'empty') {
			if (!(@unlink($file))) {
				$success = false;
			}
		}
	}
}

echo $success;