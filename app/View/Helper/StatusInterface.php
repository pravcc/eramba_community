<?php
interface StatusInterface {

	/**
	 * Get array of statuses applicable for a given item.
	 */
	public function getStatusArr($item, $allow = '*', $modelName = null);

	/**
	 * Style status labels and return.
	 */
	public function getStatuses($item, $modelName = null, $options = array());

}