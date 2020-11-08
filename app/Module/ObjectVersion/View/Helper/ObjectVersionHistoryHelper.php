<?php
App::uses('AppHelper', 'View/Helper');
class ObjectVersionHistoryHelper extends AppHelper {
	public function getUrl($model, $foreignKey) {
		return array(
			'plugin' => 'objectVersion',
			'controller' => 'objectVersion',
			'action' => 'history',
			$model,
			$foreignKey
		);
	}
}