<?php
App::uses('Router', 'Routing');

class AppRouter extends Router {
	public static function addMappedResource($urlName) {
		parent::$_resourceMapped[] = $urlName;
	}
}