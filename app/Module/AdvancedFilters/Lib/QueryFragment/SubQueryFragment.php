<?php
// namespace AdvancedFilters\Lib\QueryFragment;
// use AdvancedFilters\Lib\AbstractQueryFragment;

App::uses('AbstractQueryFragment', 'AdvancedFilters.Lib');

class SubQueryFragment extends AbstractQueryFragment {

	protected $_fragment = null;
	protected $_model = null;
	protected $_returnField = null;
	protected $_conditions = [];

	public function __construct(Model $Model, $returnField = null) {
		$this->_model = $Model;

		if ($returnField === null) {
			$returnField = $this->_model->escapeField($this->_model->primaryKey);
		}
		
		$this->returnField($returnField);
	}

	public function returnField($field = null) {
		if ($field === null) {
			return $this->_returnField;
		}

		return $this->_returnField = $this->_model->escapeField($field);
	}

	public function addCondition($condition) {
		$this->_conditions = Hash::merge($this->_conditions, $condition);

		return $this->_conditions;
	}

	public function conditions() {
		return $this->_conditions;
	}

	protected function _build() {
		if ($this->returnField() === null) {
			throw new Exception('SubQuery return field is not defined', 1);
		}

		if (empty($this->_conditions)) {
			trigger_error('SubQuery conditions are not configured');
		}

        $dataSource = $this->_model->getDataSource();

        $query = $dataSource->buildStatement([
            'table' => $dataSource->fullTableName($this->_model),
            'alias' => $this->_model->alias,
            'conditions' => $this->conditions(),
            'fields' => [
                $this->returnField()
            ]
        ], $this->_model);

        return $query;
	}

	public function getFragment() {
		return $this->_build();
	}

}
