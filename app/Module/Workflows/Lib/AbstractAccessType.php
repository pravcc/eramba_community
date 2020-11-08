<?php
/**
 * @package       Workflows.Lib
 */

abstract class AbstractAccessType {

	public function __construct() {
		
	}

	abstract public function process($foreignKey, $Model);
}
