<?php
/**
 * @package       Workflows.Config
 */

App::uses('WorkflowsModule', 'Workflows.Lib');

Cache::config('workflows_access', am(
	array(
		'duration'=> '+5 hours',
		'prefix' => 'workflows_access',
		'groups' => array('WorkflowsModule')
	), 
	Configure::read('cacheOptions')
));

Cache::config('workflows_instances', am(
	array(
		'duration'=> '+5 hours',
		'prefix' => 'workflows_instances',
		'groups' => array('WorkflowsModule')
	), 
	Configure::read('cacheOptions')
));