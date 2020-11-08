<?php
App::uses('Inflector', 'Utility');

class AdvancedFiltersQuery
{

/**
 * returns subquery string for given query type
 * 
 * @param  Model $model
 * @param  array $filter filter settings
 * @param  array $data request field data
 * @return string $query
 */
    public function get($model, $filter, $data)
    {
        $type = $filter['_config']['type'];

        $queryClass = self::getTypeClass($type);

        $queryBuilder = new $queryClass($model, $filter, $data[$filter['name']]);
        $query = $queryBuilder->get();

        return $query;
    }


    public static function getTypeClass($type)
    {
    	$queryClass = Inflector::classify($type) . 'Query';

        App::uses($queryClass, 'Lib/AdvancedFilters/Query');

        return $queryClass;
    }
}