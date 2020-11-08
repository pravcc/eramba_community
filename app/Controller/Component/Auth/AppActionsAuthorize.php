<?php

App::uses('BaseAuthorize', 'Controller/Component/Auth');
 
class AppActionsAuthorize extends BaseAuthorize
{
	public function authorize($user, CakeRequest $request)
	{
		$Acl = $this->_Collection->load('Acl');
		$groups = array('Group' => array('id' => $user['Groups']));
		return $Acl->check($groups, $this->action($request));
	}
}