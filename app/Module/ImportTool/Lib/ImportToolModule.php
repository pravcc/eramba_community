<?php
App::uses('ModuleBase', 'Lib');
class ImportToolModule extends ModuleBase {
	const VALUE_SEPARATOR = '|';

	public static function explodeValues($value) {
		if (!empty($value)) {
			$value = explode(self::VALUE_SEPARATOR, $value);
		}

		return $value;
	}

	public static function buildValues($values) {
		if (is_array($values)) {
			$values = implode(self::VALUE_SEPARATOR, $values);
		}

		return $values;
	}

	/**
	 * Generic method that formats array of $IDs => $values for import preview table as tooltips
	 * or downloaded templates.
	 * 
	 * @param  array  $types List array of IDs and values.
	 * @param  mixed   $displayAsIds True to display the formatted list as $key: $value
	 *                               False to display the formatted list as "$key" for $value
	 *                               String value to set your own format without the \n new line
	 * @return string        Formatted string.
	 */
	public static function formatList($list, $displayAsIds = true) {
		array_walk($list, 'self::processList', $displayAsIds);

		return implode('', $list);
	}

	/**
	 * Handles callback for array_walk() in formatList() method.
	 */
	public static function processList(&$item, $key, $displayAsIds) {
		if (is_bool($displayAsIds)) {
			if ($displayAsIds === true) {
				$value = sprintf("%s: %s", $key, $item);
			}
			else {
				$value = __('"%s" for %s', $key, $item);
			}
		}

		if (is_string($displayAsIds)) {
			$value = sprintf($displayAsIds, $key, $item);
		}

		$item = sprintf("\n%s", $value);
	}

}
