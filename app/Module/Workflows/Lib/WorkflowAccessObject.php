<?php
/**
 * @package       Workflows.Lib
 */

App::uses('WorkflowObject', 'Workflows.Lib');
App::uses('WorkflowAccess', 'Workflows.Model');
// App::uses('ClassRegistry', 'Utility');

class WorkflowAccessObject {
	/**
	 * Preloaded properties. Format example:
	 * 
	 * array(
	'WorkflowSetting.14' => array(
		(int) 1 => array(
			(int) 0 => '8'
		),
		(int) 3 => array(),
		(int) 4 => array(),
		(int) 5 => array(),
		(int) 6 => array()
	),
	'WorkflowStage.19' => array(
		(int) 1 => array(
			(int) 0 => '1',
			(int) 1 => '3',
			(int) 2 => '4'
		),
		(int) 3 => array(
			(int) 0 => '1',
			(int) 1 => '3',
			(int) 2 => '1',
			...
		)
	));
	 * @var array
	 */
	protected $_preloaded = [];

	public function __construct() {
		// parent::__construct();
	}

	/**
	 * Preload access properties to be later used for checks.
	 * Warning! This queries the DB and stores it. Thats so it is possible to do the check() in a view for example.
	 * 
	 * @return bool               True on success.
	 */
	public function preload(WorkflowAccess $WorkflowAccess, $model) {
		$ret = true;

		// $WorkflowAccess = ClassRegistry::init('Workflows.WorkflowAccess');
		$ret &= $preloadList = $WorkflowAccess->getObjectListByModel($model);

		foreach ($preloadList as $alias => $foreignKeys) {
			foreach ($foreignKeys as $foreignKey) {
				// we build the identifier string as $key in resulting array
				// which acts as a pointer to all accesses on a section
				$identifier = self::buildIdentifier([$alias, $foreignKey]);

				$ret &= $this->_setPreloaded(
					$identifier,
					$WorkflowAccess->parseUsers($alias, $foreignKey)
				);
			}
		}
		
		return $ret;
	}

	/**
	 * Store the accesses for later use in format: ['WorkflowStage.19' => [1 => [1,2,3]]]
	 * Where $key is unique identifier, then access type and user IDs.
	 * 
	 * @param string $key      Unique identifier.
	 * @param array  $accesses Access Types + user IDs parsed from WorkflowAccess::parseUsers()
	 */
	protected function _setPreloaded($key, $accesses) {
		if (isset($this->_preloaded[$key])) {
			trigger_error(sprintf('Preloaded key identifier "%s" already exists!', $key));
			return false;
		}

		$this->_preloaded[$key] = $accesses;

		return true;
	}

	/**
	 * Method returns the requested access data by $accessOn and $accessType
	 * 
	 * @param  array      $accessOn    Check access onto [$modelName, $foreignKey]
	 * @param  string|int $accessType  Do the count only on specific type of an access
	 *                                 @see  WorkflowAccess::accesses() for all types.
	 *                                 
	 * @return array                   Requested access information.
	 */
	protected function _getAccess($accessOn, $accessType) {
		if (!in_array($accessType, array_keys(WorkflowAccess::accesses()))) {
			trigger_error(__('Wrong $accessType provided. Please provide one of access types defined in WorkflowAccess::accesses() method.'));
		}
		// build the $key identifier to check on preloaded values
		$identifier = self::buildIdentifier($accessOn);

		// and return the desired data
		return $this->_preloaded[$identifier][$accessType];
	}

	/**
	 * Normalize $accessOn property.
	 * 
	 * @param  mixed  $accessOn Access Type - eg. owner, can edit, etc..
	 *                          @see WorkflowAccess::accesses() for all types.
	 * @return array            Normalized $accessOn property array or False on error.
	 */
	public static function nomalizeAccessOn($accessOn) {
		if (!is_array($accessOn)) {
			return false;
		}

		if (count($accessOn) != 2) {
			return false;
		}

		$accessOn = array_values($accessOn);
		return $accessOn;
	}

	/**
	 * Builds an unique identifier that is used for searching the preloaded values.
	 * 
	 * @param  array  $accessOn Access On property.
	 * @return string           Unique Identifier for searching accesses.
	 */
	public function buildIdentifier(array $accessOn) {
		$accessOn = self::nomalizeAccessOn($accessOn);
		if ($accessOn === false) {
			trigger_error('Workflow Access check has a wrong $accessOn argument. Should look like array($modelName, $foreignKey) to check access on.');

			return null;
		}

		$identifier = $accessOn[0] . '.' . $accessOn[1];
		return $identifier;
	}

	/**
	 * Method to check $accessType access for $userId (warning - for now works only on pre-reloaded data).
	 * 
	 * @param  int    $userId               Check access against user ID.
	 * @param  array  $accessOn             Check access onto [$modelName, $foreignKey]
	 * @param  string|int $accessType       Access Type - eg. owner, can edit, etc..
	 *                                      @see WorkflowAccess::accesses() for all types.
	 *                                      
	 * @return boolean                      True if user has access, False otherwise.
	 */
	public function check($userId, array $accessOn, $accessType) {
		$data = $this->_getAccess($accessOn, $accessType);

		// check if $userId is inside preloaded values
		return in_array($userId, $data);
	}

	/**
	 * Get all user IDs that belongs to $accessOn by $accessType.
	 * 
	 * @param  array  $accessOn             Check access onto [$modelName, $foreignKey]
	 * @param  string|int $accessType       Access Type - eg. owner, can edit, etc..
	 *                                      @see WorkflowAccess::accesses() for all types.
	 *                                      
	 * @return array 						List of user IDs.
	 */
	public function get(array $accessOn, $accessType) {
		return $this->_getAccess($accessOn, $accessType);
	}

	/**
	 * Count users that has $accessType on $accessOn.
	 * 
	 * @param  array      $accessOn   Counte users that has access onto [$modelName, $foreignKey]
	 * @param  string|int $accessType Do the count only on specific type of an access
	 *                                @see  WorkflowAccess::accesses() for all types.
	 *                                
	 * @return int                    The resulting count of users with specific $acessType.
	 */
	public function count(array $accessOn, $accessType) {
		$data = $this->_getAccess($accessOn, $accessType);

		// return count of values (users)
		return count($data);
	}
}
