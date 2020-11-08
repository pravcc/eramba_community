<?php
App::uses('StringTemplate', 'Lib');

/**
 * Query Templates.
 */
class QueryTemplate extends StringTemplate
{
	
/**
 * Get params.
 * 
 * @return Array Params.
 */
	public function params() {
		return $this->_params;
	}
}