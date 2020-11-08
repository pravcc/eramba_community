<?php
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');

class ObjectStatusQuery extends AbstractQuery
{
    public static $queryType = self::QUERY_OBJECT_STATUS;

    public static $defaultComparison = self::COMPARISON_EQUAL;

    public static function getComparisonTypes($extract = null, $labels = true)
    {
        $types = array(
            self::COMPARISON_EQUAL => __('Must Match'),
            self::COMPARISON_NOT_EQUAL => __('Must be Different'),
        );

        $types = parent::arrayExtract($types, $extract);
        
        return ($labels) ? $types : array_keys($types);
    }
}