<?php
App::uses('FilterCase', 'AdvancedFilters.Lib/QueryAdapter/FilterCase');
App::uses('FilterAdapter', 'AdvancedFilters.Lib/QueryAdapter');

/**
 * Extension of base filter case processor.
 */
class UserFieldCase extends FilterCase
{

/**
 * Scope matching params. 
 * Define scope params that must be in match with input params (defined in _params) to trigger of this case processor.
 * 
 * @var array
 */
	protected $_matchingParams = [
	];

/**
 * Determines if adapt query propagation have to be stopped.
 * 
 * @var boolean
 */
	protected $_stopPropagation = false;

/**
 * Adapt query for this case. Build conditions and do whatever you need to adapt query.
 * 
 * @param AdvancedQuery $query Query instance.
 * @return AdvancedQuery
 */
	protected function _adaptQuery($query)
	{
		if (!empty($this->_params['filter']['userField'])) {
			$alias = 'UserFieldsObject' . $this->_params['filter']['userField'];

            $Model = $this->_params['model'];

            $findFieldParts = explode('.', $this->_params['filter']['findField']);
            if (count($findFieldParts) > 2) {
                for ($i = 0; $i < count($findFieldParts) - 2; $i++) {
                    $Model = $Model->{$findFieldParts[$i]};
                }
            }

            $Model->bindModel([
                'hasMany' => [
                    $alias => [
                        'className' => 'UserFields.UserFieldsObject',
                        'foreignKey' => 'foreign_key',
                        'conditions' => [
                            $alias . '.model' => $Model->alias,
                            $alias . '.field' => [$this->_params['filter']['userField'], $this->_params['filter']['userField'] . 'Group'],
                        ],
                    ]
                ]
            ], false);

            $ObjectModel = ClassRegistry::init($alias);
            $ObjectModel->useTable = 'user_fields_objects';
            $ObjectModel->table = 'user_fields_objects';
		}
	}
}