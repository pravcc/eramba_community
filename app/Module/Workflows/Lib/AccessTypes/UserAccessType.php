<?php
App::uses('AbstractAccessType', 'Workflows.Lib');

class UserAccessType extends AbstractAccessType {

	/**
	 * User association process doenst require any special modifications, returns the same value user ID.
	 */
	public function process($foreignKey, $Model) {
		return $foreignKey;
	}
}
