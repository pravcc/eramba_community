<?php
/**
 * @package       Workflows.Config
 */

App::uses('AppModule', 'Lib');

// Router::connect('/workflows/v2/stages/*', array('plugin' => 'workflows', 'controller' => 'workflowStages', 'action' => 'index'));
// Router::connect('/workflows/v2/add/*', array('plugin' => 'workflows', 'controller' => 'workflowStages', 'action' => 'add'));
Router::connect('/workflows/stages/edit/*', array(
	'plugin' => 'workflows',
	'controller' => 'workflowStages',
	'action' => 'edit'
));
Router::connect('/workflows/stages/delete/*', array(
	'plugin' => 'workflows',
	'controller' => 'workflowStages',
	'action' => 'delete'
));

foreach (AppModule::instance('Workflows')->whitelist() as $model) {
	$c = controllerFromModel($model);

	Router::connect('/'.$c.'/workflows/manage', array(
		'plugin' => 'workflows',
		'controller' => 'workflowStages',
		'action' => 'index',
		$model
	));

	Router::connect('/'.$c.'/workflows/stages/add', array(
		'plugin' => 'workflows',
		'controller' => 'workflowStages',
		'action' => 'add',
		$model
	));

	Router::connect('/'.$c.'/workflows/manage/*', array(
		'plugin' => 'workflows',
		'controller' => 'workflowInstances',
		'action' => 'manage',
		$model
	));

	// Router::connect('/'.$c.'/workflows/call-stage/*', array(
	// 	'plugin' => 'workflows',
	// 	'controller' => 'workflowInstances',
	// 	'action' => 'call',
	// 	$model
	// ));

	Router::connect('/'.$c.'/workflows/request/*', array(
		'plugin' => 'workflows',
		'controller' => 'workflowInstances',
		'action' => 'handleRequest',
		$model
	));
}