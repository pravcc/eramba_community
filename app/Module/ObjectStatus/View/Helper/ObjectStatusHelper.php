<?php
App::uses('ModuleBaseHelper', 'View/Helper');
App::uses('AppModule', 'Lib');

class ObjectStatusHelper extends ModuleBaseHelper {
	public $helpers = ['Html', 'Eramba', 'LimitlessTheme.Icons'];
	public $settings = [];

	public function get($item, $model) {
		$statusConfig = $this->_View->viewVars['objectStatus'];
		$statusOk = true;
		$tags = [];

		foreach ($statusConfig as $status => $config) {
			if (isset($config['storageSelf']) && $config['storageSelf'] == false) {
				$objectStatus = Hash::extract($item['ObjectStatus'], '{n}[name=' . $status . ']');
				
				if (!empty($objectStatus) && $objectStatus[0]['status']) {
					$statusOk = false;
					$statusClass = $this->getClass($config);
					$tags[] = $this->Eramba->getLabel($config['title'], $statusClass);
				}
			}
			else {
				if ($item[$model][$status]) {
					$statusOk = false;
					$statusClass = $this->getClass($config);
					$tags[] = $this->Eramba->getLabel($config['title'], $statusClass);
				}
			}
		}

		if ($statusOk) {
			$tags[] = $this->Eramba->getLabel(__('OK'), 'success');
		}

		echo implode('<br>', $tags);
	}

	public function getClass($itemConfig) {
		$class = 'warning';

		$classMap = [
			'danger' => 'danger',
			'warning' => 'warning',
			'success' => 'success',
			'improvement' => 'improvement'
		];

		if (!empty($itemConfig['type'])) {
			$class = $classMap[$itemConfig['type']];
		}

		return $class;
	}

	public function icon($options = [])
	{
		return $this->Icons->render('pulse2', $options);
	}

	public static function isShowable($Model)
	{
		return $Model->Behaviors->loaded('ObjectStatus.ObjectStatus') && $Model->hasShowableObjectStatuses();
	}
}
