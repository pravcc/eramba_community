<?php
App::uses('FieldDataBehavior', 'Model/Behavior');
App::uses('Debugger', 'Utility');
App::uses('DebugPanel', 'DebugKit.Lib');
App::uses('DebugTimer', 'DebugKit.Lib');
App::uses('DebugMemory', 'DebugKit.Lib');

/**
 * Crud debug panel in DebugKit
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 */
class FieldDataPanel extends DebugPanel {

/**
 * Declare we are a plugin
 *
 * @var string
 */
	public $plugin = 'FieldData';

/**
 * beforeRender callback
 *
 * @param Controller $controller
 * @return void
 */
	public function beforeRender(Controller $controller) {
		$model = $controller->Crud->getSubject()->model;
		
		$fieldDataEnabled = false;
		if (!empty($model->Behaviors) && $model->Behaviors->enabled('FieldData.FieldData')) {
			$fieldDataEnabled = true;

			$debug = $model->Behaviors->FieldData->debug;
			
			$Collection = $model->getFieldCollection();
			$vars = $Collection->getViewOptions();

			$controller->set('fieldCollection', $vars['FieldDataCollection']);
			unset($vars['FieldDataCollection']);

			$controller->set('debug', $debug);
			$controller->set('formVars', $vars);
			$controller->set('fieldList', $Collection->getList());
		}

		$controller->set('fieldDataEnabled', $fieldDataEnabled);
	}

}
