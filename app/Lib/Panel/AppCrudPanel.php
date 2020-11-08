<?php
App::uses('CrudPanel', 'Crud.Lib/Panel');
App::uses('AppVariablesPanel', 'Lib/Panel');

/**
 * Crud debug panel in DebugKit
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 */
class AppCrudPanel extends CrudPanel {

	public $title = 'Crud';

	public $elementName = 'crud_panel';

/**
 * beforeRender callback
 *
 * @param Controller $controller
 * @return void
 */
	public function beforeRender(Controller $controller) {
		parent::beforeRender($controller);

		array_walk_recursive($controller->viewVars['crudDebugKitData']['events'], 'AppVariablesPanel::modify');
	}
	
}
