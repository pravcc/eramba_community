<?php
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');

class TextQuery extends AbstractQuery
{
    public static $queryType = self::QUERY_TEXT;

    public static $defaultComparison = self::COMPARISON_LIKE;

    public static function getComparisonTypes($extract = null, $labels = true)
    {
        $types = array(
            self::COMPARISON_LIKE => __('Contains'),
            self::COMPARISON_NOT_LIKE => __('Does not Contain'),
            self::COMPARISON_EQUAL => __('Must Match'),
            self::COMPARISON_NOT_EQUAL => __('Must be Different'),
            self::COMPARISON_IS_NULL => __('Is Empty'),
        );

        $types = parent::arrayExtract($types, $extract);
        
        return ($labels) ? $types : array_keys($types);
    }
}