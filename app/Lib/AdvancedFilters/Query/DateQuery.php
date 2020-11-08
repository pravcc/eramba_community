<?php
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');

class DateQuery extends AbstractQuery
{
    public static $queryType = self::QUERY_DATE;

    public static $defaultComparison = self::COMPARISON_EQUAL;

    public static function getComparisonTypes($extract = null, $labels = true)
    {
        $types = array(
            self::COMPARISON_EQUAL => __('On'),
            self::COMPARISON_ABOVE => __('After'),
            self::COMPARISON_UNDER => __('Before'),
            self::COMPARISON_IS_NULL => __('Is Empty'),
        );

        $types = parent::arrayExtract($types, $extract);
        
        return ($labels) ? $types : array_keys($types);
    }

    public static function getSpecialValues($extract = null, $labels = true)
    {
        $types = array(
            self::TODAY_VALUE => __('Today'),
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
        );

        $types = parent::arrayExtract($types, $extract);
        
        return ($labels) ? $types : array_keys($types);
    }
}