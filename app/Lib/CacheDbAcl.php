<?php
/**
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Chris Nizzardini
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Controller.Component.Acl
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AclInterface', 'Controller/Component/Acl');
App::uses('Hash', 'Utility');
App::uses('ClassRegistry', 'Utility');

/**
 * CacheDbAcl works the exact sabe as DbAcl expect that it will cache the results to using a cache config of your choosing:
 * - Configurations in bootstrap.php: 
 * App::uses('CacheDbAcl', 'Lib');
 * Configure::write('CacheDbAclConfig','Name_of_Your_Cache_Config')
 * Configure::write('CacheDbAclAro','YourArrayKey.YourArray');
 * 
 * - Configurations in core.php:
 * Configure::write('Acl.classname', 'CacheDbAcl');
 * 
 * @package       Cake.Controller.Component.Acl
 */
class CacheDbAcl extends CakeObject implements AclInterface {

/**
 * Constructor
 *
 */
	public function __construct() {
		parent::__construct();
		$this->Permission = ClassRegistry::init(array('class' => 'Permission', 'alias' => 'Permission'));
        $this->Setting = ClassRegistry::init(array('class' => 'Setting', 'alias' => 'Setting'));
		$this->Aro = $this->Permission->Aro;
		$this->Aco = $this->Permission->Aco;
	}

/**
 * Initializes the containing component and sets the Aro/Aco objects to it.
 *
 * @param AclComponent $component
 * @return void
 */
	public function initialize(Component $component) {
		$component->Aro = $this->Aro;
		$component->Aco = $this->Aco;
	}

/**
 * Checks if the given $aro has access to action $action in $aco
 *
 * @param string $aro ARO The requesting object identifier.
 * @param string $aco ACO The controlled object identifier.
 * @param string $action Action (defaults to *)
 * @return boolean Success (true if ARO has access to action in ACO, false otherwise)
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/access-control-lists.html#checking-permissions-the-acl-component
 */
	public function check($aro, $aco, $action = "*") {
        // if cache is disabled then default to normal operation
        if(Configure::read('Cache.disable') == true){
            return $this->Permission->check($aro, $aco, $action);
        }

        // we temporarily disable debug for potential cheking problems returned via ajax
        $tmpDebug = Configure::read('debug');
        Configure::write('debug', 0);
        
        // read name of cache config for AclCache
        $cacheConfig = Configure::read('CacheDbAclConfig');
        // if not found then use default
        if(!$cacheConfig){
            $cacheConfig = 'default';
        }
        
        // check which portion of $aro to use for key
        $cacheAro = Configure::read('CacheDbAclAro');
        // if not set just serialze $aro
        if(!$cacheAro){
            $cacheKey = 'CacheDbAcl_'.md5(serialize($aro).$aco.$action);
        }
        // use custom portion of $aro
        else{
            $tmp = explode('.', $cacheAro);
            $aroTmp = false;
            foreach($tmp as $i){
                if($aroTmp == false){
                	if (isset($aro[$i])) {
                    	$aroTmp = $aro[$i];
                	}
                }
                else{
                    $aroTmp = $aroTmp[$i];
                }
            }
            
            if(!isset($aroTmp) || empty($aroTmp)){
                $cacheKey = 'CacheDbAcl_'.md5(serialize($aro).$aco.$action);
            }
            else{
                $cacheKey = 'CacheDbAcl_'.md5(serialize($aroTmp).$aco.$action);
            }
        }

        // check for cache key in cache
        $check = Cache::read($cacheKey, $cacheConfig);
        
        Configure::write('debug', $tmpDebug);


        // if key exists then return value
        if( $check !== false ){
            return $check == 1;
        }
        // check database and write to cache
        else{
            $check = $this->Permission->check($aro, $aco, $action);
            Cache::write($cacheKey, $check ? 1 : 0,$cacheConfig);
        }
        
		return $check;
	}

/**
 * Allow $aro to have access to action $actions in $aco
 *
 * @param string $aro ARO The requesting object identifier.
 * @param string $aco ACO The controlled object identifier.
 * @param string $actions Action (defaults to *)
 * @param integer $value Value to indicate access type (1 to give access, -1 to deny, 0 to inherit)
 * @return boolean Success
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/access-control-lists.html#assigning-permissions
 */
	public function allow($aro, $aco, $actions = "*", $value = 1) {
		$ret = $this->Permission->allow($aro, $aco, $actions, $value);
        if ($actions == "*") {
            $this->Setting->deleteCache('acl');
        }
        
        return true;
	}

/**
 * Deny access for $aro to action $action in $aco
 *
 * @param string $aro ARO The requesting object identifier.
 * @param string $aco ACO The controlled object identifier.
 * @param string $action Action (defaults to *)
 * @return boolean Success
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/access-control-lists.html#assigning-permissions
 */
	public function deny($aro, $aco, $action = "*") {
		$ret = $this->allow($aro, $aco, $action, -1);
        
        return $ret;
	}

/**
 * Let access for $aro to action $action in $aco be inherited
 *
 * @param string $aro ARO The requesting object identifier.
 * @param string $aco ACO The controlled object identifier.
 * @param string $action Action (defaults to *)
 * @return boolean Success
 */
	public function inherit($aro, $aco, $action = "*") {
		return $this->allow($aro, $aco, $action, 0);
	}

/**
 * Allow $aro to have access to action $actions in $aco
 *
 * @param string $aro ARO The requesting object identifier.
 * @param string $aco ACO The controlled object identifier.
 * @param string $action Action (defaults to *)
 * @return boolean Success
 * @see allow()
 */
	public function grant($aro, $aco, $action = "*") {
		return $this->allow($aro, $aco, $action);
	}

/**
 * Deny access for $aro to action $action in $aco
 *
 * @param string $aro ARO The requesting object identifier.
 * @param string $aco ACO The controlled object identifier.
 * @param string $action Action (defaults to *)
 * @return boolean Success
 * @see deny()
 */
	public function revoke($aro, $aco, $action = "*") {
		return $this->deny($aro, $aco, $action);
	}

/**
 * Get an array of access-control links between the given Aro and Aco
 *
 * @param string $aro ARO The requesting object identifier.
 * @param string $aco ACO The controlled object identifier.
 * @return array Indexed array with: 'aro', 'aco' and 'link'
 */
	public function getAclLink($aro, $aco) {
		return $this->Permission->getAclLink($aro, $aco);
	}

/**
 * Get the keys used in an ACO
 *
 * @param array $keys Permission model info
 * @return array ACO keys
 */
	protected function _getAcoKeys($keys) {
		return $this->Permission->getAcoKeys($keys);
	}

}
