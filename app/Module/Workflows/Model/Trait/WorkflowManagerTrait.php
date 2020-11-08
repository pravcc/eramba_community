<?php
App::Uses('Hash', 'Utility');
App::Uses('Wf_ManageAccess', 'Workflows.Lib');

trait WorkflowManagerTrait {

	/**
	 * Get managers data from database and filter everything through association classes to get only user IDs.
	 */
	public function getManagers($accessType = null, $id = null) {
		$belongsTo = $this->getAssociated('belongsTo');
		$belongsTo = $belongsTo[0];
		$assoc = $this->getAssociated($belongsTo);

		$conds = [];
		if ($accessType !== null) {
			$conds = [
				$this->alias . '.access_type' => $accessType
			];
		}

		if ($id !== null) {
			$conds[$this->getManagersFkName()] = $id;
		}

		$this->virtualFields['apply_to_id'] = $this->getManagersFkName();
		$list = $this->find('all', [
			'conditions' => $conds,
			'fields' => [
				$this->alias . '.type',
				$this->alias . '.foreign_key',
				$this->alias . '.access_type',
				$this->alias . '.apply_to_id'
			],
			'recursive' => -1
		]);

		
		$values = Hash::extract($list, '{n}.{s}');
		
		/**
		 * Values variable below is now an array formatted in this way:
		 array(
			(int) 0 => array(
				'type' => 'user',
				'foreign_key' => '1',
				'access_type' => '1',

				// virtual field alias for getManagersFkName()
				// its easier to use on more than one model instance for this management 
				'apply_to_id' => '1',
			),
			...
		 */
		return new Wf_ManageAccess($values, $this);
	}

	/**
	 * @alias for $this->_getForeignKey() with model alias prepended already.
	 */
	public function getManagersFkName() {
		return $this->alias . '.' . $this->_getForeignKey();
	}

	/**
	 * Get the foreign key for the first defined belongsTo association.
	 */
	protected function _getForeignKey() {
		$belongsTo = $this->getAssociated('belongsTo');
		$belongsTo = $belongsTo[0];
		$assoc = $this->getAssociated($belongsTo);

		return $assoc['foreignKey'];
	}

}