<?php
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');

class QuerySettings
{

/**
 * query settings data
 */
    public $model = null;
    public $comparisonType = '';
    public $comparisonField = '';
    public $comparisonData = array();
    public $returnField = '';
    public $conditions = array();
    public $assocType = '';
    public $mainQuery = false;

/**
 * construct
 * 
 * @param boolean $mainQuery
 */
    public function __construct($mainQuery = false)
    {
        $this->mainQuery = $mainQuery;
    }

/**
 * builds query settings data
 * 
 * @param  Model $model
 * @param  array $filter filter settings
 * @param  array $data request field data
 * @return QuerySettings $this
 */
    public function build($model, $filter, $data)
    {
        if (isset($filter['path'])) {
            // $queries = [];
            // if ($filter['comp_type'] == AbstractQuery::COMPARISON_ALL_IN || $filter['comp_type'] == AbstractQuery::COMPARISON_NOT_ALL_IN ) {
            //     foreach ($data as $dataItem) {
            //         $comparison = $filter['comp_type'];
            //         $lastModelItem = null;
            //         $compData = $dataItem;
            //         foreach (array_reverse($filter['path']) as $modelItem => $item) {
            //             $query = new QuerySettings();
            //             $query->model = ClassRegistry::init($modelItem);
            //             $query->comparisonData = [$compData];
            //             $query->comparisonType = $comparison;
            //             $query->comparisonField = $this->_extractField($item['findField']);
            //             $query->returnField = $this->_extractField($item['field']);
            //             $comparison = AbstractQuery::COMPARISON_IN;
            //             $mainQuery = false;
            //             $lastModelItem = $modelItem;
            //             $compData = $query;
            //         }

            //         $queries[] = $query;
            //     }

            //     $query = new QuerySettings();
            //     $query->model = ClassRegistry::init($lastModelItem);
            //     $query->comparisonData = $queries;
            //     $query->comparisonType = ($filter['comp_type'] == AbstractQuery::COMPARISON_NOT_ALL_IN) ? AbstractQuery::COMPARISON_NOT_IN : AbstractQuery::COMPARISON_IN;
            //     $query->comparisonField = $this->_extractField($item['field']);
            //     $query->returnField = $this->_extractField($item['field']);
            //     $query->mainQuery = true;

            //     return $query;
            // }
            // else {
                $comparison = $filter['comp_type'];
                foreach (array_reverse($filter['path']) as $modelItem => $item) {
                    $query = new QuerySettings();
                    $query->model = ClassRegistry::init($modelItem);
                    $query->comparisonData = $data;
                    $query->comparisonType = $comparison;
                    $query->comparisonField = $this->_extractField($item['findField']);
                    $query->returnField = $this->_extractField($item['field']);
                    $query->conditions = (!empty($item['conditions'])) ? $item['conditions'] : array();
                    $data = $query;
                    $comparison = AbstractQuery::COMPARISON_IN;
                    $mainQuery = false;
                }

                $data->mainQuery = true;
            // }

            // if ($filter['comp_type'] == AbstractQuery::COMPARISON_ALL_IN || $filter['comp_type'] == AbstractQuery::COMPARISON_NOT_ALL_IN ) {
            //     $data->comparisonType = $filter['comp_type'];
            // }

            return $data;
        }

        if ($filter['_config']['type'] == 'object_status') {
            $this->_buildObjectStatus($model, $filter, $data);
        }
        else {
            $modelName = $this->_extractModel($filter['findField']);
            $assoc = $model->getAssociated($modelName);

            $assocType = (!empty($assoc['association'])) ? $assoc['association'] : 'default';
            $this->assocType = $assocType;

            $this->{'_build' . Inflector::camelize($assocType)}($filter, $data, $assoc); 
        }

        return $this;
    }

/**
 * UserFields settings build
 *
 * @param  Model $Model
 * @param  array $userField User field name.
 * @param  array $data Request field data.
 * @return array Array of QuerySetting.
 */
    public static function getUserFieldsSettings($Model, $userField, $data) {
        $userData = [];
        $groupData = [];
        $queries = [];

        $data = (array) $data;
        foreach ($data as $key => $item) {
            if (strpos($item, 'User-') !== false) {
                $userData[] = str_replace('User-', '', $item);
            }
            if (strpos($item, 'Group-') !== false) {
                $groupData[] = str_replace('Group-', '', $item);
            }
        }

        if (!empty($userData)) {
            $userQuerySettings = new QuerySettings();
            $userQuerySettings->model = ClassRegistry::init('UserFields.UserFieldsUser');;
            $userQuerySettings->comparisonData = $userData;
            $userQuerySettings->comparisonType = AbstractQuery::COMPARISON_IN;
            $userQuerySettings->comparisonField = 'user_id';
            $userQuerySettings->returnField = 'foreign_key';
            $userQuerySettings->conditions = [
                'UserFieldsUser.field' => $userField,
                'UserFieldsUser.model' => $Model->alias,
            ];

            $queries[] = $userQuerySettings;
        }

        if (!empty($groupData)) {
            $groupQuerySettings = new QuerySettings();
            $groupQuerySettings->model = ClassRegistry::init('UserFields.UserFieldsGroup');;
            $groupQuerySettings->comparisonData = $groupData;
            $groupQuerySettings->comparisonType = AbstractQuery::COMPARISON_IN;
            $groupQuerySettings->comparisonField = 'group_id';
            $groupQuerySettings->returnField = 'foreign_key';
            $groupQuerySettings->conditions = [
                'UserFieldsGroup.field' => $userField . 'Group',
                'UserFieldsGroup.model' => $Model->alias,
            ];

            $queries[] = $groupQuerySettings;
        }

        return $queries;
    }

/**
 * ObjectStatus settings build
 *
 * @param  Model $Model
 * @param  array $filter filter settings
 * @param  array $data request field data
 * @return void
 */
    protected function _buildObjectStatus($Model, $filter, $data)
    {
        $statusConfig = $Model->getObjectStatusConfig();
        $statusField = (!empty($statusConfig[$filter['statusField']]['field'])) ? $statusConfig[$filter['statusField']]['field'] : $filter['statusField'];

        $this->model = ClassRegistry::init('ObjectStatus.ObjectStatus');
        $this->comparisonData = $data;
        $this->comparisonType = $filter['comp_type'];
        $this->comparisonField = 'status';
        $this->returnField = 'foreign_key';
        $this->conditions = [
            'ObjectStatus.name' => $statusField,
            'ObjectStatus.model' => $Model->alias,
        ];
    }

/**
 * default settings build
 * 
 * @param  array $filter filter settings
 * @param  array $data request field data
 * @param  array $assoc association data
 * @return void
 */
    protected function _buildDefault($filter, $data, $assoc = null)
    {
        $modelName = $this->_extractModel($filter['findField']);

        $this->model = ClassRegistry::init($modelName);
        $this->comparisonData = $data;
        $this->comparisonType = $filter['comp_type'];
        $this->comparisonField = $this->_extractField($filter['findField']);
        $this->returnField = $this->_extractField($filter['field']);

        $this->conditions = (!empty($assoc['conditions'])) ? $assoc['conditions'] : array();
    }

/**
 * BelongsTo settings build
 * 
 * @param  array $filter filter settings
 * @param  array $data request field data
 * @param  array $assoc association data
 * @return void
 */
    protected function _buildBelongsTo($filter, $data, $assoc = null)
    {
        $this->_buildDefault($filter, $data, $assoc);

        $this->returnField = 'id';
    }

/**
 * HasOne settings build
 * 
 * @param  array $filter filter settings
 * @param  array $data request field data
 * @param  array $assoc association data
 * @return void
 */
    protected function _buildHasOne($filter, $data, $assoc = null)
    {
        $this->_buildDefault($filter, $data, $assoc);

        $this->returnField = $assoc['foreignKey'];
    }

/**
 * HasMany settings build
 * 
 * @param  array $filter filter settings
 * @param  array $data request field data
 * @param  array $assoc association data
 * @return void
 */
    protected function _buildHasMany($filter, $data, $assoc = null)
    {
        $this->_buildDefault($filter, $data, $assoc);

        $this->returnField = $assoc['foreignKey'];
    }

/**
 * HasAndBelongsToMany settings build
 * 
 * @param  array $filter filter settings
 * @param  array $data request field data
 * @param  array $assoc association data
 * @return void
 */
    protected function _buildHasAndBelongsToMany($filter, $data, $assoc = null)
    {
        $this->_buildDefault($filter, $data, $assoc);

        $subComparison = $filter['comp_type'];
        if ($filter['comp_type'] == AbstractQuery::COMPARISON_IS_NULL) {
            $subComparison = AbstractQuery::COMPARISON_IS_NOT_NULL;
        }

        $subQuerySettings = new QuerySettings();
        $subQuerySettings->model = $this->model;
        $subQuerySettings->comparisonData = $this->comparisonData;
        $subQuerySettings->comparisonType = $subComparison;
        $subQuerySettings->comparisonField = $this->comparisonField;
        $subQuerySettings->returnField = 'id';

        $mainComparison = $filter['comp_type'];
        if ($filter['comp_type'] != AbstractQuery::COMPARISON_ALL_IN && $filter['comp_type'] != AbstractQuery::COMPARISON_NOT_ALL_IN ) {
            $mainComparison = AbstractQuery::COMPARISON_IN;
        }

        // $this->model = $this->model->{$assoc['with']};
        $this->model = ClassRegistry::init($assoc['with']);
        $this->comparisonData = $subQuerySettings;
        $this->comparisonType = $mainComparison;
        $this->comparisonField = $assoc['associationForeignKey'];
        $this->returnField = $assoc['foreignKey'];
    }

/**
 * extracts model name from field string
 * 
 * @param  string $field
 * @param  int $level depth
 * @return string
 */
    protected function _extractModel($field, $level = 1)
    {
        $fields = explode('.', $field);

        return $fields[$level-1];
    }

/**
 * extracts field name from field string
 * @param  string $field
 * @return string
 */
    protected function _extractField($field)
    {
        $fields = explode('.', $field);

        return end($fields);
    }
}