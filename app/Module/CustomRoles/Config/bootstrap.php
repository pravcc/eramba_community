<?php
App::uses('CustomRolesModule', 'CustomRoles.Lib');

Cache::config('custom_roles', am(
	array(
		'duration'=> '+1 hours',
		'prefix' => 'custom_roles_',
		'groups' => array('CustomRoles')
	), 
	Configure::read('cacheOptions')
));