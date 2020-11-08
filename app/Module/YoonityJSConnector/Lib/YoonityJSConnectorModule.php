<?php
App::uses('ModuleBase', 'Lib');
App::uses('File', 'Utility');
App::uses('Configure', 'Core');

class YoonityJSConnectorModule extends ModuleBase
{
	/**
	 * Set actual version of YoonityJS Framework
	 */
	public static function setYjsVersion()
	{
		if (empty(Configure::read('YoonityJS.version'))) {
			$versionFile = new File(WWW_ROOT . 'js' . DS . 'YoonityJS' . DS . 'VERSION.txt');
			if ($versionFile->exists()) {
				$yjsVersionFile = trim($versionFile->read());
				$fileLines = preg_split("/((\r?\n)|(\r\n?))/", $versionFile->read());
				foreach ($fileLines as $line) {
					if (strpos($line, 'VERSION: ') === 0) {
						$yjsVersionTemp = explode('VERSION: ', $line);
						if (count($yjsVersionTemp) == 2) {
							$yjsVersion = $yjsVersionTemp[1];
							Configure::write('YoonityJS.version', $yjsVersion);

							break;
						}
					}
				}
			}
		}
	}
}
