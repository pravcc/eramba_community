<?php
Router::connect('/dashboard', array(
	'plugin' => 'dashboard',
	'controller' => 'dashboardKpis',
	'action' => 'user'
));

Router::connect('/dashboard/user', array(
	'plugin' => 'dashboard',
	'controller' => 'dashboardKpis',
	'action' => 'user'
));

Router::connect('/dashboard/add/*', array(
	'plugin' => 'dashboard',
	'controller' => 'dashboardKpis',
	'action' => 'add'
));

Router::connect('/dashboard/edit/*', array(
	'plugin' => 'dashboard',
	'controller' => 'dashboardKpis',
	'action' => 'edit'
));

Router::connect('/dashboard/admin', array(
	'plugin' => 'dashboard',
	'controller' => 'dashboardKpis',
	'action' => 'admin'
));

Router::connect('/dashboard/sync/*', array(
	'plugin' => 'dashboard',
	'controller' => 'dashboardKpis',
	'action' => 'sync'
));

Router::connect('/dashboard/calendar', array(
	'plugin' => 'dashboard',
	'controller' => 'dashboardCalendarEvents',
	'action' => 'index'
));

Router::connect('/dashboard/store-logs', array(
	'plugin' => 'dashboard',
	'controller' => 'dashboardKpis',
	'action' => 'store_logs'
));

Router::connect('/dashboard/recalculate-values', array(
	'plugin' => 'dashboard',
	'controller' => 'dashboardKpis',
	'action' => 'recalculate_values'
));