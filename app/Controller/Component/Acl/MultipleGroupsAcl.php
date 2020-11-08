<?php
App::uses('AclInterface', 'Controller/Component/Acl');
App::uses('DbAcl', 'Controller/Component/Acl');
App::uses('Hash', 'Utility');
App::uses('ClassRegistry', 'Utility');

/**
 * ACL implementation for multiple groups per user functionality.
 * 
 * @package       Controller.Component.Acl
 */
class MultipleGroupsAcl extends DbAcl implements AclInterface {

/**
 * Constructor
 */
	public function __construct() {
		parent::__construct();

		$this->Permission = ClassRegistry::init(array('class' => 'AppPermission', 'alias' => 'Permission'));
		$this->Aro = $this->Permission->Aro;
		$this->Aco = $this->Permission->Aco;
	}

	/**
	 * Method returns formatted list of conflicting ACOs by defined AROs.
	 * 
	 * @param  array $aroIDs List of AROs.
	 * @return array         Formatted list of conflicting ACO actions
	 */
	public function conflicts($aroIDs)
	{
		$conflicts = $this->Permission->conflicts($aroIDs);

		return Hash::extract($conflicts, '{n}.0.conflict_alias');
	}

}
