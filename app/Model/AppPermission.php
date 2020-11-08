<?php
App::uses('Permission', 'Model');

/**
 * Permissions linking AROs with ACOs compatible with app's multiple groups functionality.
 *
 * @package       Model
 */
class AppPermission extends Permission {
	public $alias = 'Permission';

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Extended method for ACL check but modified for app's use,
	 * where a single forbidden permission has priority.
	 * 
	 * @return bool         True if the provided set of AROs are allowed to access ACOs, false otherwise
	 */
	public function check($aro, $aco, $action = '*') {
		if (!$aro || !$aco) {
			return false;
		}

		$permKeys = $this->getAcoKeys($this->schema());
		$aroPath = $this->Aro->node($aro);
		$acoPath = $this->Aco->node($aco);

		if (!$aroPath) {
			$this->log(__d('cake_dev',
					"%s - Failed ARO node lookup in permissions check. Node references:\nAro: %s\nAco: %s",
					'DbAcl::check()',
					print_r($aro, true),
					print_r($aco, true)),
				E_USER_WARNING
			);
			return false;
		}

		if (!$acoPath) {
			$this->log(__d('cake_dev',
					"%s - Failed ACO node lookup in permissions check. Node references:\nAro: %s\nAco: %s",
					'DbAcl::check()',
					print_r($aro, true),
					print_r($aco, true)),
				E_USER_WARNING
			);
			return false;
		}

		if ($action !== '*' && !in_array('_' . $action, $permKeys)) {
			$this->log(__d('cake_dev', "ACO permissions key %s does not exist in %s", $action, 'DbAcl::check()'), E_USER_NOTICE);
			return false;
		}

		return $this->_checkPaths($aroPath, $acoPath, $action);
	}

	/**
	 * Method checks permissions in between $acoPath and $aroPaths compatible with new multiple groups feature.
	 * 
	 * @param  array $aroPath  ARO paths defining multiple groups.
	 * @param  array $acoPath  Ordinary ACO path.
	 * @return bool            Is permission allowed or denied.
	 */
	protected function _checkPaths($aroPath, $acoPath, $action) {
		$permKeys = $this->getAcoKeys($this->schema());

		$acoIDs = Hash::extract($acoPath, '{n}.' . $this->Aco->alias . '.id');

		$count = count($aroPath);
		$inherited = array();
		$permissionList = [];

		for ($i = 0; $i < $count; $i++) {
			$aroId = $aroPath[$i][$this->Aro->alias]['id'];
			$permissionList[] = $this->_checkAroPermission($aroId, $acoIDs);
		}
		
		// we return true in the end in case there is no forbidden permission configured
		// for the given set of AROs and their permissions
		$allowAccess = in_array(true, $permissionList);
		
		return $allowAccess;
	}

	/**
	 * Check permission for a single ARO ID for a set of ACO IDs.
	 * 
	 * @return bool    Is ARO allowed to access or not.
	 */
	protected function _checkAroPermission($aroID, $acoIDs) {
		$permAlias = $this->alias;
		$perms = $this->find('all', array(
			'conditions' => array(
				"{$permAlias}.aro_id" => $aroID,
				"{$permAlias}.aco_id" => $acoIDs
			),
			'order' => array($this->Aco->alias . '.lft' => 'desc'),
			'recursive' => 0
		));

		if (empty($perms)) {
			return false;
		}

		$perms = Hash::extract($perms, '{n}.' . $this->alias . '._read');

		// lets pull out the deepest configured permission for the certaing ACO
		$perm = $perms[0];

		// simplified version for multiple groups ACL check
		// ultimately if there is at least one forbidden access in the set of permissions,
		// final result is denied access
		return ($perm != -1);
	}

	/**
	 * Method gets the list of conflicting permissions in provided actions (ACOs).
	 * 
	 * @param  array $aroIDs  Array of AROs.
	 * @return array          Conflicting ACOs.
	 */
	public function conflicts($aroIDs)
	{
		$permAlias = $this->alias;

		$this->Aco->Behaviors->load('Search.Searchable');

		// find descendands of a 'controller' node
		$subQuery = $this->Aco->getQuery('all', [
			'conditions' => [
				'Aco.parent_id' => 1
			],
			'fields' => [
				'Aco.id'
			],
			'recursive' => -1
		]);

		$perms = $this->find('all', [
			'conditions' => [
				// check all provided aro IDs (groups)
				"{$permAlias}.aro_id" => $aroIDs,

				// choose only specifically defined permissions, no inheritance
				"{$permAlias}._read !=" => 0,

				// only second level descendands of 'controller' node
				"Aco.parent_id IN ({$subQuery})" 
			],
			'having' => [
				// get only those records that have some differences in _read permission
				"COUNT(DISTINCT({$permAlias}._read)) > 1",

				// its enough to get records that have more than 1 permission set
				"COUNT({$permAlias}.aco_id) > 1"
			],
			'fields' => [
				'CONCAT(ParentAco.alias, "/", Aco.alias) as conflict_alias'
			],
			'group' => [
				// group actions (ACOs) for which groups request permission
				"{$permAlias}.aco_id"
			],
			'joins' => [
				[
					'table' => 'acos',
					'alias' => 'Aco',
					'type' => 'LEFT',
					'conditions' => [
						"Aco.id = {$permAlias}.aco_id"
					]
				],
				[
					'table' => 'acos',
					'alias' => 'ParentAco',
					'type' => 'LEFT',
					'conditions' => [
						"ParentAco.id = Aco.parent_id"
					]
				],
			],
			'recursive' => -1
		]);

		return $perms;
	}
}