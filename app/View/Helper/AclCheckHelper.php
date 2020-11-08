<?php
App::uses('AppHelper', 'View/Helper');
App::uses('AclCheck', 'Lib/Acl');

class AclCheckHelper extends AppHelper
{

    /**
     * Check permission for given groups to access given url.
     *
     * @param array|string $url Url to check.
     * @param array $groups List of group ids for which we want to check permissions.
     * @return bool true accessible, false forbidden
     */
    public function check($url, $groups = null)
    {
        if ($groups === null) {
            $groups = $this->_View->get('logged')['Groups'];
        }

        return AclCheck::check($url, $groups);
    }

}