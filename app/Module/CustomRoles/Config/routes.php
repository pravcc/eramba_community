<?php
/**
 * @package       CustomRoles.Config
 */

App::uses('AppModule', 'Lib');

foreach (AppModule::instance('CustomRoles')->whitelist() as $model) {
	$c = controllerFromModel($model);
}