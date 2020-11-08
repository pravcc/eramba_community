<?php
App::uses('AdvancedQuery', 'AdvancedQuery.Lib');
App::uses('QueryCondition', 'AdvancedQuery.Lib/Template');

/**
* Filter Adapter for AdvancedQuery.
*/
class FilterAdapter
{

/**
 * Special values.
 */
    const EMPTY_VALUE = '';
    const NULL_VALUE = '_null_';
    const TODAY_VALUE = '_today_';
    const THIS_YEAR_VALUE = '_this_year_';
    const PLUS_1_DAYS_VALUE = '_plus_1_days_';
    const PLUS_3_DAYS_VALUE = '_plus_3_days_';
    const PLUS_7_DAYS_VALUE = '_plus_7_days_';
    const PLUS_14_DAYS_VALUE = '_plus_14_days_';
    const PLUS_30_DAYS_VALUE = '_plus_30_days_';
    const MINUS_1_DAYS_VALUE = '_minus_1_days_';
    const MINUS_3_DAYS_VALUE = '_minus_3_days_';
    const MINUS_7_DAYS_VALUE = '_minus_7_days_';
    const MINUS_14_DAYS_VALUE = '_minus_14_days_';
    const MINUS_30_DAYS_VALUE = '_minus_30_days_';

/**
 * Comparison types.
 */
    const COMPARISON_EQUAL = 0;
    const COMPARISON_NOT_EQUAL = 6;
    const COMPARISON_ABOVE = 1;
    const COMPARISON_UNDER = 2;
    const COMPARISON_LIKE = 3;
    const COMPARISON_NOT_LIKE = 4;
    const COMPARISON_IN = 5;
    const COMPARISON_NOT_IN = 7;
    const COMPARISON_ALL_IN = 8;
    const COMPARISON_NOT_ALL_IN = 9;
    const COMPARISON_IS_NULL = 10;
    const COMPARISON_IS_NOT_NULL = 11;
    const COMPARISON_ONLY_IN = 12;
    const COMPARISON_NOT_ONLY_IN = 13;

/**
 * Comparison sign map.
 * 
 * @var Array
 */
    public static $_comparisonSign = [
        self::COMPARISON_EQUAL => '=',
        self::COMPARISON_NOT_EQUAL => '=',
        self::COMPARISON_ABOVE => '>',
        self::COMPARISON_UNDER => '<',
        self::COMPARISON_LIKE => 'LIKE',
        self::COMPARISON_NOT_LIKE => 'LIKE',
        self::COMPARISON_IN => 'IN',
        self::COMPARISON_NOT_IN => 'IN',
        self::COMPARISON_ALL_IN => 'IN',
        self::COMPARISON_NOT_ALL_IN => 'IN',
        self::COMPARISON_ONLY_IN => 'IN',
        self::COMPARISON_NOT_ONLY_IN => 'IN',
        self::COMPARISON_IS_NULL => 'IS NOT NULL',
        self::COMPARISON_IS_NOT_NULL => 'IS NOT NULL',
    ];

/**
 * Check if comparison type is negative.
 *
 * @param String|int $comparison Conparison type.
 * @return boolean
 */
    public static function isNegativeComparison($comparison) {
        $list = [
            self::COMPARISON_NOT_EQUAL,
            self::COMPARISON_NOT_LIKE,
            self::COMPARISON_NOT_IN,
            self::COMPARISON_NOT_ALL_IN,
            self::COMPARISON_IS_NULL,
            self::COMPARISON_NOT_ONLY_IN,
            self::COMPARISON_IS_NULL,
        ];

        return in_array($comparison, $list);
    }

/**
 * Get condition query for advanced filters.
 * Loop through all Case porcessors and find once which fits to our input params and create query.
 * 
 * @param Model $Model Model instance.
 * @param Array $filter Advanced filters field config.
 * @param Array $data Request data.
 * @return String Query.
 */
    public function getQuery($Model, $filter, $data) {
        $params = $this->_parseParams($Model, $filter, $data);

        $filterCases = [
            'UserFieldCase',
            'EmptyCase',
            'NullCase',
            'ObjectStatusCase',
            'ThisYearCase',
            'TodayCase',
            'DateCase',
            'MultipleCase',
            'LikeCase',
            'FilterCase',
        ];

        $query = new AdvancedQuery($params['model'], 'all', [
            'fields' => [$params['returnField']]
        ]);

        foreach ($filterCases as $caseClass) {
            App::uses($caseClass, 'AdvancedFilters.Lib/QueryAdapter/FilterCase');

            $case = new $caseClass($params);

            if ($case->match()) {
                $query = $case->adaptQuery($query);

                if ($case->stopPropagation()) {
                    break;
                }
            }
        }

        // negate query
        if (self::isNegativeComparison($params['comparisonType'])) {
            $query = $this->_negateQuery($query);
        }

        return $query->getQuery();
    }

/**
 * Parse input params to one config array.
 * 
 * @param Model $Model Model instance.
 * @param Array $filter Advanced filters field config.
 * @param Array $data Request data.
 * @return Array Parsed working config.
 */
    protected function _parseParams($Model, $filter, $data) {
        if (!empty($filter['statusField'])) {
            $filter['findField'] = "ObjectStatus.{$filter['statusField']}";
        }

        $findFieldParts = explode('.', $filter['findField']);
        $findField = $findFieldParts[count($findFieldParts) - 2] . '.' . $findFieldParts[count($findFieldParts) - 1];

        array_pop($findFieldParts);
        $findFieldModel = (count($findFieldParts) == 1) ? $findFieldParts[0] : $findFieldParts;

        $params = [
            'type' => $filter['_config']['type'],
            'model' => $Model,
            'returnField' => $filter['field'],
            'findField' => $findField,
            'findFieldModel' => $findFieldModel,
            'findValue' => $data[$filter['name']],
            'comparisonType' => $this->_getComparisonType($filter),
            'filter' => $filter
        ];

        return $params;
    }

/**
 * Get comparison type from filter config.
 * 
 * @param Array $filter Advanced filters field config.
 * @return int Comparison type.
 */
    protected function _getComparisonType($filter) {
        $comp = null;

        if (isset($filter['comp_type'])) {
            $comp = $filter['comp_type'];
        }
        elseif ($filter['_config']['type'] == 'text') {
            $comp = self::COMPARISON_LIKE;
        }
        elseif ($filter['_config']['type'] == 'multiple_select') {
            $comp = self::COMPARISON_IN;
        }
        else {
            $comp = self::COMPARISON_EQUAL;
        }

        return $comp;
    }

/**
 * If query has negative comparison we build query with normal positive comparison and here we extend query to nagate the query result.
 * 
 * @param AdvancedQuery $query Query instance.
 * @return AdvancedQuery
 */
    protected function _negateQuery($query) {
        $field = $query->options()['fields'][0];

        $negativeQuery = new AdvancedQuery($query->model(), 'all', [
            'fields' => [
                $field
            ],
            'conditions' => [
                QueryCondition::comparison($field, 'NOT IN', $query)
            ]
        ]);

        return $negativeQuery;
    }

    public static function getSpecialValueLabels() {
        return [
            self::NULL_VALUE => __('Empty'),
            self::TODAY_VALUE => __('Today'),
            self::THIS_YEAR_VALUE => __('This Year'),
            self::PLUS_1_DAYS_VALUE => __('+1 Day'),
            self::PLUS_3_DAYS_VALUE => __('+3 Days'),
            self::PLUS_7_DAYS_VALUE => __('+7 Days'),
            self::PLUS_14_DAYS_VALUE => __('+14 Days'),
            self::PLUS_30_DAYS_VALUE => __('+30 Days'),
            self::MINUS_1_DAYS_VALUE => __('-1 Day'),
            self::MINUS_3_DAYS_VALUE => __('-3 Days'),
            self::MINUS_7_DAYS_VALUE => __('-7 Days'),
            self::MINUS_14_DAYS_VALUE => __('-14 Days'),
            self::MINUS_30_DAYS_VALUE => __('-30 Days'),
        ];
    }

}