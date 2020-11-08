<?php

App::uses('BaseAuthenticate', 'Controller/Component/Auth');

class OauthGoogleAuthenticate extends BaseAuthenticate
{
	protected $_gsOauthUserData = null;

	/**
	 * Authenticates the identity contained in a request. Will use the `settings.userModel`, and `settings.fields`
	 * to find POST data that is used to find a matching record in the `settings.userModel`. Will return false if
	 * there is no post data, either username or password is missing, or if the scope conditions have not been met.
	 *
	 * @param CakeRequest $request The request that contains login information.
	 * @param CakeResponse $response Unused response object.
	 * @return mixed False on login failure. An array of User data on success.
	 */
	public function authenticate(CakeRequest $request, CakeResponse $response)
	{
		return $this->checkUser($request, $response);
	}

	public function checkUser(CakeRequest $request, CakeResponse $response)
	{
		if (empty($this->_gsOauthUserData)) {
			$this->_authenticateOauthUser($request, $response);
		}

		if (isset($this->_gsOauthUserData['email'])) {
			return $this->_findUser($this->_gsOauthUserData['email']);
		}

		return false;
	}

	protected function _authenticateOauthUser(CakeRequest $request, CakeResponse $response)
	{
		$gClient = $this->settings['gClient'];
		$sessionKey = $this->settings['sessionKey'];
		$tokenSessionKey = $this->settings['tokenSessionKey'];

		if (isset($request->query['code'])) {
			$gClient->authenticate($request->query['code']);
			CakeSession::write($tokenSessionKey, $gClient->getAccessToken());
		}

		if ($gClient->getAccessToken()) {
			$gsOauth = new Google_Service_Oauth2($gClient);
			$this->_gsOauthUserData = $gsOauth->userinfo->get();
		}
	}
}
