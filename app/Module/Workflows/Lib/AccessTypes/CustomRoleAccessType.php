<?php
App::uses('AbstractAccessType', 'Workflows.Lib');

class CustomRoleAccessType extends AbstractAccessType {

	public function __construct() {
		
	}

	public function process($foreignKey, $Model) {
		return [];
		return $foreignKey;
	}
}
