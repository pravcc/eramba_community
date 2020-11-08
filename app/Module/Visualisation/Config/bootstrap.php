<?php
App::uses('VisualisationModule', 'Visualisation.Lib');

Cache::config('visualisation', am(
	array(
		'duration'=> '+1 week',
		'prefix' => 'visualisation_',
		'groups' => array('Visualisation')
	), 
	Configure::read('cacheOptions')
));

Configure::write('Visualisation.Acl.classname', 'Visualisation.VisualisationDbAcl');
App::uses('VisualisationDbAcl', 'Visualisation.Controller/Component/Acl');