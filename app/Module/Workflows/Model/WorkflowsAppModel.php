<?php
/**
 * @package       Workflows.Model
 */

App::uses('AppModel', 'Model');

class WorkflowsAppModel extends AppModel {

	public function beforeFind($query) {
		// WorkflowStageStepCondition::findByModel() for example
		$this->optimizeJoins($query);

		return $query;
	}

	public function optimizeJoins(&$query) {
		// repair joins that have a strings as keys
		if (isset($query['joins']) && is_array($query['joins'])) {
			$joins = $query['joins'];
			ksort($joins);
			$query['joins'] = array_values($joins);
		}

		return $query;
	}

	/**
	 * Just a wrapper method that re-uses getByModelQuery() method and finds list by it automatically.
	 * 
	 * @param  string $model Model name.
	 * @return array         Result data.
	 */
	public function findByModelQuery($model) {
		return $this->find('list', $this->getByModelQuery($model));
	}

	/**
	 * Validates if there is at least one value selected within multiple HABTM fields.
	 * 
	 * @param  array  $checkModels HABTM Model aliases to check in $this->data
	 * @return bool                True if one or more values are selected, False if no items selected.
	 */
	/*public function validateMultipleFields($checkModels = []) {
		$ret = false;
		foreach ($checkModels as $check) {
			if (!isset($this->data[$this->alias][$check])) {
				continue;
			}

			$val = (array) $this->data[$this->alias][$check];
			$val = array_filter($val);
			
			$ret = $ret || count($val);
		}

		if (!$ret) {
			$this->invalidate(reset($checkModels) , __('Please choose at least one User, Group or Custom Role.'));
		}

		return $ret;
	}*/
}
