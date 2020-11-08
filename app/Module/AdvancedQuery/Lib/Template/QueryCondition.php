<?php
App::uses('QueryTemplate', 'AdvancedQuery.Lib/Template');
App::uses('AdvancedQuery', 'AdvancedQuery.Lib');
App::uses('ConnectionManager', 'Model');

/**
 * Set of query templates.
 */
class QueryCondition
{

/**
 * Base comparison.
 * 
 * @param String $field Comparison field.
 * @param String $comparison Comparison sign.
 * @param mixed $value Value.
 * @return QueryTemplate
 */
	public static function comparison($field, $comparison, $value) {
		$valueTemplate = '{value}';

		// data source for sanitization
		$ds = ConnectionManager::getDataSource('default');

		if (is_string($value)) {
			// sanitize value
			$value = $ds->value($value);
		}

		if (is_array($value)) {
			$valueTemplate = "({value})";

			// string vals fix
			foreach ($value as $key => $val) {
				if (!is_numeric($val)) {
					// sanitize value
					$value[$key] = $ds->value($val);
				}
			}

			$value = implode(', ', $value);
		}

		if ($value instanceof AdvancedQuery) {
			$valueTemplate = "({value})";
		}

		return new QueryTemplate("{field} {comparison} $valueTemplate", compact('field', 'comparison', 'value'));
	}

/**
 * Date comparison.
 * 
 * @param String $field Comparison field.
 * @param String $comparison Comparison sign.
 * @param mixed $value Value.
 * @return QueryTemplate
 */
	public static function dateComparison($field, $comparison, $value) {
		if (strtotime($value) !== false) {
			// sanitize value
			$ds = ConnectionManager::getDataSource('default');
			$value = $ds->value($value);
		}

		return new QueryTemplate('DATE({field}) {comparison} DATE({value})', compact('field', 'comparison', 'value'));
	}

/**
 * Year comparison.
 * 
 * @param String $field Comparison field.
 * @param String $comparison Comparison sign.
 * @param mixed $value Value.
 * @return QueryTemplate
 */
	public static function yearComparison($field, $comparison, $value) {
		if (strtotime($value) !== false) {
			// sanitize value
			$ds = ConnectionManager::getDataSource('default');
			$value = $ds->value($value);
		}

		return new QueryTemplate('YEAR({field}) {comparison} YEAR({value})', compact('field', 'comparison', 'value'));
	}

/**
 * Like comparison.
 * 
 * @param String $field Comparison field.
 * @param String $value Value.
 * @return QueryTemplate
 */
	public static function like($field, $value) {
		// sanitize value
		$ds = ConnectionManager::getDataSource('default');
		$value = $ds->value("%{$value}%");

		return new QueryTemplate('{field} LIKE {value}', compact('field', 'value'));
	}

/**
 * Is not null comparison.
 * 
 * @param String $field Comparison field.
 * @return QueryTemplate
 */
	public static function isNotNull($field) {
		return new QueryTemplate('{field} IS NOT NULL', compact('field'));
	}

/**
 * Is null comparison.
 * 
 * @param String $field Comparison field.
 * @return QueryTemplate
 */
	public static function isNull($field) {
		return new QueryTemplate('{field} IS NULL', compact('field'));
	}
}