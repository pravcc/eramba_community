<?php
App::uses('BaseAuthenticate', 'Controller/Component/Auth');
App::uses('CakeSession', 'Model/Datasource');

class SamlAuthenticate extends BaseAuthenticate
{
	protected $_samlUserData = null;
	protected $spBaseUrl = "";
	protected $samlConfig = [];
	protected $AuthObj = null;

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
		if (empty($this->_samlUserData)) {
			$this->_decodeSamlResponse($request, $response);
		}

		if (isset($this->_samlUserData['email'])) {
			return $this->_findUser($this->_samlUserData['email']);
		}

		return false;
	}

	protected function _decodeSamlResponse()
	{
		$sessionKey = $this->settings['sessionKey'];
		$requestID = CakeSession::read($sessionKey . '.AuthNRequestID');
		$AuthObj = $this->settings['AuthObj'];
		$emailAttr = Hash::get($this->settings, 'attributes.email');

	    $AuthObj->processResponse($requestID);

	    $errors = $AuthObj->getErrors();

	    if (!empty($errors)) {
	    }

		$attrs = $AuthObj->getAttributes();
	    if ($AuthObj->isAuthenticated() && isset($attrs[$emailAttr][0])) {
	    	$this->_samlUserData['email'] = $attrs[$emailAttr][0];
	    }

	    CakeSession::delete($sessionKey . '.AuthNRequestID');
	}
}
