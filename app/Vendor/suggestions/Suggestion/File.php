<?php
namespace Suggestion;

class File {
	public function __construct($options = array()) {
	}

	public static function listVendors($model = null) {
		
		$dir = new \Folder(APP . 'Vendor' . DS . 'suggestions' . DS . 'Suggestion' . DS . 'Package' . DS . $model);
		$files = $dir->find('.*\.php');

		$arr = array();
		foreach ($files as $file) {
			$className = explode('.', $file);
			$className = $className[0];
			$namespace = 'Suggestion\Package\\' . $model . '\\' . $className;

			$class = new $namespace();
			if (!empty($class->name)) {
				$arr[$class->className] = array(
					'name' => $class->name,
					'value' => $class->className,
					'data-description' => $class->description
				);
			}
		}

		return $arr;

		/*$options = array();
		foreach ($files as $file) {
			$notificationSystemMgt = $this->Components->load('NotificationSystemMgt');
			$class = $notificationSystemMgt->loadClass($file, $type);

			$options[$file] = array(
				'name' => $class->title,
				'value' => $file,
				'data-description' => $class->description
			);
			unset($class);
		}

		return $options;*/
	}
}