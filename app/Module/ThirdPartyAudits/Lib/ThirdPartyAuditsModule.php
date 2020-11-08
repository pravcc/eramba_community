<?php
App::uses('ModuleBase', 'Lib');
App::uses('AuthComponent', 'Controller/Component');

class ThirdPartyAuditsModule extends ModuleBase {
	public static function getSessionKey() {
		return 'Auth.ComplianceAudit';
	}

	public static function setAuthSessionKey() {
		AuthComponent::$sessionKey = self::getSessionKey();
	}

	public static function allowAction(Controller &$controller) {
		if (($user = $controller->Session->read(self::getSessionKey())) !== null) {
			$controller->Auth->allow($controller->request->params['action']);
			$controller->logged = $user;
			$controller->set('logged', $controller->logged);
		}
	}
}
