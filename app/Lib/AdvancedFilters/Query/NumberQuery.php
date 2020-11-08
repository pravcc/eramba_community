<?php
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');

class NumberQuery extends AbstractQuery
{
    public static $queryType = self::QUERY_NUMBER;

    public static $defaultComparison = self::COMPARISON_EQUAL;

    public static function getComparisonTypes($extract = null, $labels = true)
    {
        $types = array(
            self::COMPARISON_EQUAL => __('Equal'),
            self::COMPARISON_ABOVE => __('Above'),
            self::COMPARISON_UNDER => __('Under'),
            self::COMPARISON_IS_NULL => __('Is Empty'),
        );

        $types = parent::arrayExtract($types, $extract);
        
        return ($labels) ? $types : array_keys($types);
    }
}