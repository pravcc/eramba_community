<?php
App::uses('Validation', 'Utility');

/**
 * Customized App Validation class.
 * 
 * @package       app.Utility
 */
class AppValidation extends Validation {

	/**
	 * Modified regex pattern for hostname validation to allow non-qualified hostnames.
	 * @var array
	 */
	protected static $_pattern = array(
		'hostname' => '(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])'
	);

	public static function url($check, $strict = false) {
		static::_populateIp();
		$validChars = '([' . preg_quote('!"$&\'()*+,-.@_:;=~[]') . '\/0-9\p{L}\p{N}]|(%[0-9a-f]{2}))';
		$regex = '/^(?:(?:https?|ftps?|ldaps?|sftp|file|news|gopher):\/\/)' . (!empty($strict) ? '' : '?') .
			'(?:' . static::$_pattern['IPv4'] . '|\[' . static::$_pattern['IPv6'] . '\]|' . static::$_pattern['hostname'] . ')(?::[1-9][0-9]{0,4})?' .
			'(?:\/?|\/' . $validChars . '*)?' .
			'(?:\?' . $validChars . '*)?' .
			'(?:#' . $validChars . '*)?$/iu';
		return static::_check($check, $regex);
	} 
}