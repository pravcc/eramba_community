<?php
App::uses('FilterCase', 'AdvancedFilters.Lib/QueryAdapter/FilterCase');
App::uses('AdvancedQuery', 'AdvancedQuery.Lib');
App::uses('QueryCondition', 'AdvancedQuery.Lib/Template');
App::uses('FilterAdapter', 'AdvancedFilters.Lib/QueryAdapter');

/**
 * Extension of base filter case processor.
 */
class DateCase extends FilterCase
{

/**
 * Scope matching params. 
 * Define scope params that must be in match with input params (defined in _params) to trigger of this case processor.
 * 
 * @var array
 */
	protected $_matchingParams = [
		'type' => [
			'date'
		],
	];

	protected $_positiveOffsetValues = [
		FilterAdapter::PLUS_1_DAYS_VALUE,
		FilterAdapter::PLUS_3_DAYS_VALUE,
		FilterAdapter::PLUS_7_DAYS_VALUE,
		FilterAdapter::PLUS_14_DAYS_VALUE,
		FilterAdapter::PLUS_30_DAYS_VALUE,
	];

	protected $_negativeOffsetValues = [
		FilterAdapter::MINUS_1_DAYS_VALUE,
		FilterAdapter::MINUS_3_DAYS_VALUE,
		FilterAdapter::MINUS_7_DAYS_VALUE,
		FilterAdapter::MINUS_14_DAYS_VALUE,
		FilterAdapter::MINUS_30_DAYS_VALUE,
	];

/**
 * Adapt query for this case. Build conditions and do whatever you need to adapt query.
 * 
 * @param AdvancedQuery $query Query instance.
 * @return AdvancedQuery
 */
	protected function _adaptQuery($query) {
		$value = $this->_processValue($this->_params['findValue']);

		$query->advancedWhere([
			QueryCondition::dateComparison(
				$this->_params['findField'],
				FilterAdapter::$_comparisonSign[$this->_params['comparisonType']],
				$value
			),
			$this->_customFieldCondition(),
			$this->_lastCommentConditions(),
			$this->_lastAttachmentsConditions(),
		], $this->_params['findFieldModel']);

		//in this case we need additional condition
		if (in_array($this->_params['findValue'], $this->_positiveOffsetValues) 
			&& $this->_params['comparisonType'] == FilterAdapter::COMPARISON_UNDER
		) {
			$query->advancedWhere([
				QueryCondition::dateComparison(
					$this->_params['findField'],
					'>=',
					'CURDATE()'
				)
			], $this->_params['findFieldModel']);
		}

		//in this case we need additional condition
		if (in_array($this->_params['findValue'], $this->_negativeOffsetValues) 
			&& $this->_params['comparisonType'] == FilterAdapter::COMPARISON_ABOVE
		) {
			$query->advancedWhere([
				QueryCondition::dateComparison(
					$this->_params['findField'],
					'<=',
					'CURDATE()'
				)
			], $this->_params['findFieldModel']);
		}

		return $query;
	}

	protected function _lastCommentConditions()
	{
		$conditions = [];

		if ($this->_params['findField'] == 'LastComment.created') {
			$assoc = $this->_params['model']->getAssociated('LastComment');

			$subQuery = new AdvancedQuery($this->_params['model']->LastComment, 'all', [
				'conditions' => $assoc['conditions'],
				'fields' => ['MAX(LastComment.id)'],
				'group' => ['LastComment.foreign_key']
			]);

			$conditions[] = "LastComment.id IN ($subQuery)";
		}

		return $conditions;
	}

	protected function _lastAttachmentsConditions()
	{
		$conditions = [];

		if ($this->_params['findField'] == 'LastAttachment.created') {
			$assoc = $this->_params['model']->getAssociated('LastAttachment');

			$subQuery = new AdvancedQuery($this->_params['model']->LastAttachment, 'all', [
				'conditions' => $assoc['conditions'],
				'fields' => ['MAX(LastAttachment.id)'],
				'group' => ['LastAttachment.foreign_key']
			]);

			$conditions[] = "LastAttachment.id IN ($subQuery)";
		}

		return $conditions;
	}

/**
 * Process special date values.
 * 
 * @param mixed $value Value.
 * @return String Converted value.
 */
	protected function _processValue($value) {
		if (!in_array($value, $this->_positiveOffsetValues) && !in_array($value, $this->_negativeOffsetValues)) {
			return $value;
		}

		$valueParts = array_values(array_filter(explode('_', $value)));

		$sign = ($valueParts[0] == 'plus') ? '+' : '-';
		$numberOfDays = $valueParts[1];

		$value = "CURDATE() $sign INTERVAL $numberOfDays DAY";

		return $value;
	}

}