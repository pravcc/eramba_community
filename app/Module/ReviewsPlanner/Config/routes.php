<?php
Router::connect('/reviews/:action/*', array(
	'plugin' => 'reviews_planner',
	'controller' => 'reviewsPlanner'
));
Router::connect('/reviews/index/*', array(
	'plugin' => 'reviews_planner',
	'controller' => 'reviewsPlanner',
	'action' => 'index'
));

// Router::connect('/security-policies/reviews/:action/*', array(
// 	'plugin' => 'reviews_planner',
// 	'controller' => 'reviewsPlanner',
// 	'SecurityPolicy'
// ));