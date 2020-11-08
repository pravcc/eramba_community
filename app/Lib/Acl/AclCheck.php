<?php
App::uses('Configure', 'Core');
App::uses('AclRouter', 'Acl.Lib');

class AclCheck
{
    /**
     * Acl instance.
     * 
     * @var AclInterface
     */
    protected static $_aclInstance = null;

    /**
     * Toggle if acl check is enabled.
     * 
     * @var boolean
     */
    protected static $_enabled = true;

    /**
     * Disable acl check.
     * 
     * @return void
     */
    public static function disable()
    {
        static::$_enabled = false;
    }

    /**
     * Enable acl check.
     * 
     * @return void
     */
    public static function enable()
    {
        static::$_enabled = true;
    }

    /**
     * Set ACL instance. Instance for future check calls.
     * 
     * @param AclInterface $adapter
     */
    public static function setAclInstance($adapter)
    {
        static::$_aclInstance = $adapter;
    }

    /**
     * Check permission to acces given $url for given $groups.
     *
     * @param array|string $url Url to check.
     * @param array $groups List of group ids for which we want to check permissions.
     * @return bool true accessible, false forbidden
     */
    public static function check($url, $groups)
    {
        return static::_check($url, $groups);
    }

    /**
     * Execute permission check of groups to access url, cache result for better performance.
     * 
     * @param array|string $url Url to check.
     * @param array $groups List of group ids for which we want to check permissions.
     * @return bool true accessible, false forbidden
     */
    public function _check($url, $groups)
    {
        // allow every url if check is disabled
        if (static::$_enabled === false) {
            return true;
        }

        // allow url from different host
        $fullCheckUrl = parse_url(Router::url($url, true));
        $fullBaseUrl = parse_url(Router::url('/', true));
        if (empty($fullCheckUrl['host']) || empty($fullBaseUrl['host']) || $fullCheckUrl['host'] != $fullBaseUrl['host']) {
            return true;
        }

        $groups = (array) $groups;

        // sort groups because we want cache key to not be dependent on order of groups
        sort($groups);

        $aro = [
            'Group' => [
                'id' => $groups
            ]
        ];

        $aco = AclRouter::aco_path($url);

        $cacheKey = 'action_permission_' . md5(implode(',', $groups) . $aco);

        if (($permission = Cache::read($cacheKey, 'acl')) === false) {
            // acl permission check
            $permission = static::$_aclInstance->check($aro, $aco);

            Cache::write($cacheKey, $permission, 'acl');
        }

        return $permission;
    }

}