<?php
/**
 * Attempts to load autoloader.
 *
 * @return boolean
 */
return function () {
    $file = dirname(__DIR__) . DS . 'upgrade' . DS . 'vendor' . DS . 'autoload.php';
    if (file_exists($file)) {
    	require_once $file;
    	return true;
    }
    
    return false;
};