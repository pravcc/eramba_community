<?php
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');

class MultipleSelectQuery extends AbstractQuery
{
    public static $queryType = self::QUERY_MULTIPLE_SELECT;

    public static $defaultComparison = self::COMPARISON_IN;

    public static function getComparisonTypes($extract = null, $labels = true)
    {
        $types = array(
            self::COMPARISON_IN => __('Includes'),
            self::COMPARISON_NOT_IN => __('Does not Include'),
            self::COMPARISON_ALL_IN => __('Must be'),
            self::COMPARISON_NOT_ALL_IN => __('Must not be'),
            self::COMPARISON_IS_NULL => __('Is Empty'),
            self::COMPARISON_IS_NOT_NULL => __('Is not Empty'),
        );

        $types = parent::arrayExtract($types, $extract);

        return ($labels) ? $types : array_keys($types);
    }
}