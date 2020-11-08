<?php
App::uses('QuerySettings', 'Lib/AdvancedFilters/Query');

abstract class AbstractQuery
{

/**
 * comparison types
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

/**
 * query types
 */
    const QUERY_TEXT = 1;
    const QUERY_NUMBER = 2;
    const QUERY_DATE = 3;
    const QUERY_SELECT = 4;
    const QUERY_MULTIPLE_SELECT = 5;
    const QUERY_OBJECT_STATUS = 6;

/**
 * empty val
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
    const IN_PLUS_7_DAYS_VALUE = '_in_plus_7_days_';
    const IN_PLUS_14_DAYS_VALUE = '_in_plus_14_days_';
    const IN_PLUS_30_DAYS_VALUE = '_in_plus_30_days_';
    const IN_MINUS_7_DAYS_VALUE = '_in_minus_7_days_';
    const IN_MINUS_14_DAYS_VALUE = '_in_minus_14_days_';
    const IN_MINUS_30_DAYS_VALUE = '_in_minus_30_days_';

/**
 * comparison query signs
 */
    protected static $_comparisonSign = [
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
        self::COMPARISON_IS_NULL => 'IS NOT NULL',
        self::COMPARISON_IS_NOT_NULL => 'IS NOT NULL',
    ];

/**
 * list of negative comparison types
 */
    protected static $_negativeComparison = [
        self::COMPARISON_NOT_EQUAL,
        self::COMPARISON_NOT_LIKE,
        self::COMPARISON_NOT_IN,
        self::COMPARISON_NOT_ALL_IN,
        self::COMPARISON_IS_NULL
    ];

/**
 * Special values replacement map.
 */
    protected static $_specialValuesMap = [
        self::TODAY_VALUE => 'DATE({{field}}) {{comparison}} DATE(CURDATE())',
        self::THIS_YEAR_VALUE => 'YEAR({{field}}) {{comparison}} YEAR(CURDATE())',
        self::PLUS_7_DAYS_VALUE => 'DATE({{field}}) {{comparison}} DATE(CURDATE() + INTERVAL 7 DAY)',
        self::PLUS_14_DAYS_VALUE => 'DATE({{field}}) {{comparison}} DATE(CURDATE() + INTERVAL 14 DAY)',
        self::PLUS_30_DAYS_VALUE => 'DATE({{field}}) {{comparison}} DATE(CURDATE() + INTERVAL 30 DAY)',
        self::MINUS_7_DAYS_VALUE => 'DATE({{field}}) {{comparison}} DATE(CURDATE() - INTERVAL 7 DAY)',
        self::MINUS_14_DAYS_VALUE => 'DATE({{field}}) {{comparison}} DATE(CURDATE() - INTERVAL 14 DAY)',
        self::MINUS_30_DAYS_VALUE => 'DATE({{field}}) {{comparison}} DATE(CURDATE() - INTERVAL 30 DAY)',
        self::IN_PLUS_7_DAYS_VALUE => 'DATE({{field}}) < CURDATE() + INTERVAL 7 DAY AND DATE({{field}}) >= CURDATE()',
        self::IN_PLUS_14_DAYS_VALUE => 'DATE({{field}}) < CURDATE() + INTERVAL 14 DAY AND DATE({{field}}) >= CURDATE()',
        self::IN_PLUS_30_DAYS_VALUE => 'DATE({{field}}) < CURDATE() + INTERVAL 30 DAY AND DATE({{field}}) >= CURDATE()',
        self::IN_MINUS_7_DAYS_VALUE => 'DATE({{field}}) >= CURDATE() - INTERVAL 7 DAY AND DATE({{field}}) < CURDATE()',
        self::IN_MINUS_14_DAYS_VALUE => 'DATE({{field}}) >= CURDATE() - INTERVAL 14 DAY AND DATE({{field}}) < CURDATE()',
        self::IN_MINUS_30_DAYS_VALUE => 'DATE({{field}}) >= CURDATE() - INTERVAL 30 DAY AND DATE({{field}}) < CURDATE()',
    ];

/**
 * Special values replacement map.
 */
    protected static $_comparisonSpecialValuesMap = [
        self::COMPARISON_UNDER => [
            self::PLUS_7_DAYS_VALUE => 'DATE({{field}}) <= CURDATE() + INTERVAL 7 DAY AND DATE({{field}}) >= CURDATE()',
            self::PLUS_14_DAYS_VALUE => 'DATE({{field}}) <= CURDATE() + INTERVAL 14 DAY AND DATE({{field}}) >= CURDATE()',
            self::PLUS_30_DAYS_VALUE => 'DATE({{field}}) <= CURDATE() + INTERVAL 30 DAY AND DATE({{field}}) >= CURDATE()',
        ],
        self::COMPARISON_ABOVE => [
            self::MINUS_7_DAYS_VALUE => 'DATE({{field}}) >= CURDATE() - INTERVAL 7 DAY AND DATE({{field}}) <= CURDATE()',
            self::MINUS_14_DAYS_VALUE => 'DATE({{field}}) >= CURDATE() - INTERVAL 14 DAY AND DATE({{field}}) <= CURDATE()',
            self::MINUS_30_DAYS_VALUE => 'DATE({{field}}) >= CURDATE() - INTERVAL 30 DAY AND DATE({{field}}) <= CURDATE()',
        ]
    ];

/**
 * query type
 */
    public static $queryType = null;

/**
 * default comparison types
 */
    public static $defaultComparison = null;

/**
 * input working model
 */
    protected $_model = null;

/**
 * filter settings
 */
    protected $_filter = [];

/**
 * request field data
 */
    protected $_requestData = [];

/**
 * comparison types for inherited query types
 *
 * @param  array $extract array of selected comparison types
 * @param  boolean $labels on true returns labels, on false returns keys
 * @return array
 */
    // abstract public static function getComparisonTypes($extract = null, $labels = true);

    public function __construct($modelName, $filter, $requestData)
    {
        $this->_model = ClassRegistry::init($modelName);
        $this->_filter = $filter;
        $this->_requestData = $requestData;

        $this->_setDefaults();
    }

/**
 * static extract
 * @access static
 */
    public static function arrayExtract($options, $extract) {
        if ($extract === null) {
            return $options;
        }

        $finalOpts = [];
        foreach ($options as $key => $value) {
            if (in_array($key, $extract)) {
                $finalOpts[$key] = $value;
            }
        }
        return $finalOpts;
    }

/**
 * sets empty default vaules
 */
    protected function _setDefaults()
    {
        if (!isset($this->_filter['comp_type'])) {
            $this->_filter['comp_type'] = static::$defaultComparison;
        }
    }

/**
 * returns query final query string
 * 
 * @return string $query
 */
    public function get()
    {
        $querySettings = new QuerySettings(true);
        $querySettings = $querySettings->build(
            $this->_model,
            $this->_filter,
            $this->_requestData
        );

        $query = $this->_getSubQuery($querySettings);

        return $query;
    }

/**
 * determines if input comparison type is negative comparison
 * 
 * @param  int $comparison
 * @return boolean
 */
    protected function isNegativeComparison($comparison)
    {
        return in_array($comparison, self::$_negativeComparison);
    }

/**
 * model preprocess to get sub query
 * 
 * @param  Model $model
 * @return void
 */
    protected function _prepareModel($model)
    {
        if (!$model->Behaviors->loaded('Search.Searchable')) {
            $model->Behaviors->attach('Search.Searchable');
        }
    }

/**
 * returns subquery string for input settings
 * 
 * @param  QuerySettings $settings query settings
 * @return string $query
 */
    protected function _getSubQuery(QuerySettings $settings)
    {
        $model = $settings->model;
        $this->_prepareModel($model);
        
        $returnField = $model->alias . '.' . $settings->returnField;

        $ds = $model->getDataSource();

        $query = $ds->buildStatement([
            'table' => $ds->fullTableName($model),
            'alias' => $model->alias,
            'conditions' => $this->_getConditions($settings),
            'fields' => [
                $returnField
            ],
            'group' => $this->_getGroupBy($settings),
        ], $model);

        if ($settings->mainQuery && $this->isNegativeComparison($this->_filter['comp_type'])) {
            $query = $this->_getNegativeSubQuery($query, $settings);
        }

        // debug($query);

        return $query;
    }

/**
 * returns inverted return data set
 * 
 * @param  string $query query to be inverted
 * @param  QuerySettings $settings query settings
 * @return string $query
 */
    protected function _getNegativeSubQuery($query, QuerySettings $settings)
    {
        $model = $this->_model;
        $returnField = $model->alias . '.' . $settings->returnField;

        $ds = $model->getDataSource();

        $query = $ds->buildStatement([
            'table' => $ds->fullTableName($model),
            'alias' => $model->alias,
            'conditions' => [
                $this->_filter['field'] . ' NOT IN (' . $query . ')'
            ],
            'fields' => [
                $this->_filter['field']
            ],
        ], $model);

        return $query;
    }

/**
 * query group by data
 * 
 * @param  QuerySettings $settings query settings
 * @return array
 */
    protected function _getGroupBy(QuerySettings $settings)
    {
        $groupBy = '';

        $model = $settings->model;
        $returnField = $model->alias . '.' . $settings->returnField;

        if ($settings->mainQuery && in_array($settings->comparisonType, [self::COMPARISON_IN, self::COMPARISON_NOT_IN])) {
            $groupBy = $returnField . ' HAVING COUNT(' . $returnField . ') >= ' . 1;
        }
        elseif ($settings->mainQuery && in_array($settings->comparisonType, [self::COMPARISON_ALL_IN, self::COMPARISON_NOT_ALL_IN])) {
            $groupBy = $returnField . ' HAVING COUNT(' . $returnField . ') >= ' . count($this->_requestData);//count($settings->comparisonData)
        }

        return $groupBy;
    }

/**
 * query conditions data
 * 
 * @param  QuerySettings $settings query settings
 * @return array
 */
    protected function _getConditions(QuerySettings $settings)
    {
        $conditions = $settings->conditions;

        //user fields handle
        if (!empty($this->_filter['userField']) && $settings->comparisonField == $this->_filter['userField']) {
            $settings->comparisonData = QuerySettings::getUserFieldsSettings(
                $settings->model,
                $this->_filter['userField'],
                $settings->comparisonData
            );
            $settings->comparisonField = 'id';
        }

        $field = $settings->model->alias . '.' . $settings->comparisonField;
        $compSign = self::$_comparisonSign[$settings->comparisonType];
        // if (is_array($settings->comparisonData) && is_object($settings->comparisonData[0])) {
        //     $dataItems = [];
        //     foreach ($settings->comparisonData as $item) {
        //         $dataItems[] = $field . ' ' . $compSign . ' (' . $this->_getSubQuery($item) . ')';
        //     }
        //     $conditions[] = implode(' AND ', $dataItems);
        //     return $conditions;
        // }
        // else {
            $data = (is_object($settings->comparisonData)) ? $this->_getSubQuery($settings->comparisonData) : $settings->comparisonData;
        // }
        
        if (is_array($settings->comparisonData) && !empty($settings->comparisonData[0]) && is_object($settings->comparisonData[0])) {
            foreach ($settings->comparisonData as $key => $value) {
                $settings->comparisonData[$key] = $this->_getSubQuery($value);
            }
            $data = implode(' UNION ', $settings->comparisonData);
        }
        
        if ($data == self::NULL_VALUE) {
            $conditions[] = $field . ' IS NULL';
            return $conditions;
        }

        if (in_array($settings->comparisonType, [self::COMPARISON_LIKE, self::COMPARISON_NOT_LIKE])) {
            $data = '%' . $data . '%';
        }

        if (in_array($settings->comparisonType, [self::COMPARISON_IN, self::COMPARISON_NOT_IN, self::COMPARISON_ALL_IN, self::COMPARISON_NOT_ALL_IN]) && is_string($data)) {
            $conditions[] = $field . ' ' . $compSign . ' (' . $data . ')';
        }
        elseif (in_array($settings->comparisonType, [self::COMPARISON_IS_NULL, self::COMPARISON_IS_NOT_NULL])) {
            $textEmptyConditions = (static::$queryType == self::QUERY_TEXT) ? ' AND ' . $field . ' != ""' : '';
            $conditions[] = $field . ' ' . $compSign . $textEmptyConditions;
        }
        elseif ($data == self::EMPTY_VALUE) {
            $conditions[] = $field . ' ' . $compSign;
        }
        elseif (!empty(static::$_comparisonSpecialValuesMap[$settings->comparisonType][$data])) {
            $cond = static::$_comparisonSpecialValuesMap[$settings->comparisonType][$data];
            //replace field in teplate
            $cond = str_replace('{{field}}', $field, $cond);
            $conditions[] = $cond;
        }
        elseif (in_array($data, array_keys(static::$_specialValuesMap))) {
            $cond = static::$_specialValuesMap[$data];
            //replace field in teplate
            $cond = str_replace('{{field}}', $field, $cond);
            //replace comparison in teplate
            $cond = str_replace('{{comparison}}', $compSign, $cond);
            $conditions[] = $cond;
        }
        else {
            if ($settings->comparisonType == self::COMPARISON_EQUAL && static::$queryType == self::QUERY_DATE) {
                $field = "DATE({$field})";
            }
            $conditions[$field . ' ' . $compSign] = $data;
        }

        return $conditions;
    }

/**
 * special values labels
 */
    public static function getSpecialValueLabel($value = null) {
        $labels = [
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

        return ($value === null) ? $labels : $labels[$value];
    }

/**
 * comparison labels
 */
    public static function getComparisonLabel($comparison = null) {
        $labels = [
            self::COMPARISON_EQUAL => __('Must Match'),
            self::COMPARISON_NOT_EQUAL => __('Must be Different'),
            self::COMPARISON_ABOVE => __('Is Above'),
            self::COMPARISON_UNDER => __('Is Under'),
            self::COMPARISON_LIKE => __('Contains'),
            self::COMPARISON_NOT_LIKE => __('Does not Contain'),
            self::COMPARISON_IN => __('Includes'),
            self::COMPARISON_NOT_IN => __('Does not Include'),
            self::COMPARISON_ALL_IN => __('Must be'),
            self::COMPARISON_NOT_ALL_IN => __('Must not be'),
            self::COMPARISON_IS_NULL => __('Is Empty'),
            self::COMPARISON_IS_NOT_NULL => __('Is not Empty'),
        ];

        return ($comparison === null) ? $labels : $labels[$comparison];
    }
}