<?php
App::uses('Component', 'Controller');
App::uses('ClassRegistry', 'Utility');
App::uses('SamlConnector', 'Model');
App::uses('CakeSession', 'Model/Datasource');

class SamlAuthComponent extends Component
{
	public $components = array('Session');

	protected $sessionKey = 'SamlConnector';

	protected $SamlConnector;

	protected $samlConfig = [];

	protected $activeSamlData = null;

	protected $loginRedirectUrls = array(
		'main' => '/login',
		'va' => '/portal/vendor-assessments/login',
		'ar' => '/portal/account-reviews/login'
	);

	public function initialize(Controller $controller)
	{
		$this->controller = $controller;

		//
		// Initialize SamlConnector model and get data of active Saml Connector
		$this->SamlConnector = ClassRegistry::init('SamlConnector');
		$data = $this->SamlConnector->getActiveSamlData();

		if ($data !== false) {
			$this->activeSamlData = $data;
		} else {
			return false;
		}
		//
		
		//
		// SAML configuration
		$spBaseUrl = Router::fullBaseUrl();

	    $this->samlConfig = array_merge($this->samlConfig, [
	        'sp' => [
	            'entityId' => $spBaseUrl . '/samlConnectors/getMetadata',
	            'assertionConsumerService' => [
	                'url' => $spBaseUrl . '/login',
	            ],
	            // 'singleLogoutService' => [
	            //     'url' => $spBaseUrl . '/users/logout',
	            // ],
	            'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
	        ],
	        'idp' => [
	            'entityId' => $this->activeSamlData['identity_provider'],
	            'singleSignOnService' => [
	                'url' => $this->activeSamlData['remote_sign_in_url'],
	            ],
	            // 'singleLogoutService' => [
	            //     'url' => $this->activeSamlData['remote_sign_out_url'],
	            // ],
	            'x509cert' => $this->activeSamlData['idp_certificate'],
	        ]
	    ]);

	    if ($this->activeSamlData['sign_saml_request'] == 1 &&
			!empty($this->activeSamlData['sp_certificate']) &&
			!empty($this->activeSamlData['sp_private_key'])) {
	    	$this->samlConfig['security']['authnRequestsSigned'] = true;
	    	$this->samlConfig['sp']['x509cert'] = $this->activeSamlData['sp_certificate'];
	    	$this->samlConfig['sp']['privateKey'] = $this->activeSamlData['sp_private_key'];
	    } else {
	    	$this->samlConfig['security']['authnRequestsSigned'] = false;
	    }

	    if ($this->activeSamlData['validate_saml_request'] == 1) {

	    }
	}

	public function getMetadata()
	{
		$Settings = new \OneLogin\Saml2\Settings($this->samlConfig);
		$metadata = $Settings->getSPMetadata();
		$errors = $Settings->validateMetadata($metadata);
		if (empty($errors)) {
			header('Content-Type: text/xml');
			echo $metadata;
		} else {
			throw new \OneLogin\Saml2\Error(__('Invalid SP metadata: %s', implode(', ', $errors)), \OneLogin\Saml2\Error::METADATA_SP_INVALID);
		}
	}

	public function login()
	{
		if (isset($this->controller->request->query['portal']) &&
			array_key_exists($this->controller->request->query['portal'], $this->loginRedirectUrls)) {
			$this->setLoginRedirectUrl($this->loginRedirectUrls[$this->controller->request->query['portal']], false);
		}
		
		$AuthObj = $this->getAuthObj()->login();

	    $ssoBuiltUrl = $auth->login(null, array(), false, false, true);
		CakeSession::write($this->getSessionKey() . '.AuthNRequestID', $AuthObj->getLastRequestID());

	    header('Pragma: no-cache');
	    header('Cache-Control: no-cache, must-revalidate');
	    header('Location: ' . $ssoBuiltUrl);
	    exit();
	}

	public function logout()
	{
		$returnTo = null;
	    $parameters = array();
	    $nameId = CakeSession::read($this->getSessionKey() . '.samlNameId');
	    $sessionIndex = CakeSession::read($this->getSessionKey() . '.samlSessionIndex');
	    $nameIdFormat = CakeSession::read($this->getSessionKey() . '.samlNameIdFormat');
	    $nameIdNameQualifier = CakeSession::read($this->getSessionKey() . '.samlNameIdNameQualifier');
	    $nameIdSPNameQualifier = CakeSession::read($this->getSessionKey() . '.samlNameIdSPNameQualifier');;

	    $this->getAuthObj()->logout($returnTo, $parameters, $nameId, $sessionIndex, false, $nameIdFormat, $nameIdNameQualifier, $nameIdSPNameQualifier);
	}

	public function getAuthObj()
	{
		return new \OneLogin\Saml2\Auth($this->samlConfig);
	}

	public function getLoginRedirectUrls($full = true)
	{
		$redirectUrls = array();
		if ($full) {
			foreach ($this->loginRedirectUrls as $ru) {
				$redirectUrls[] = Router::fullBaseUrl() . $ru;
			}
		} else {
			$redirectUrls = array_values($this->loginRedirectUrls);
		}

		return $redirectUrls;
	}

	public function getLoginRedirectUrl($which)
	{
		return array_key_exists($which, $this->loginRedirectUrls) ? $this->loginRedirectUrls[$which] : false;
	}

	public function setLoginRedirectUrl($url, $cakeFormat = true)
	{
		if ($cakeFormat) {
			$url = Router::url($url);
		}

		if (!in_array($url, $this->loginRedirectUrls)) {
			return false;
		}

		$fullUrl = Router::fullBaseUrl() . $url;

		$this->samlConfig['sp']['assertionConsumerService']['url'] = $fullUrl;

		return true;
	}

	public function getSessionKey()
	{
		return $this->sessionKey;
	}

	public function getSamlAuthUrl()
	{
		$url = Router::url();
		$query = "";
		foreach ($this->loginRedirectUrls as $key => $ru) {
			if ($ru === $url) {
				$query = "?portal=" . $key;
				break;
			}
		}
		return Router::fullBaseUrl() . "/samlConnectors/singleSingOn" . $query;
	}

	public function getSamlLogoutUrl()
	{
		return Router::fullBaseUrl() . "/samlConnectors/singleLogout";
	}

	public function getActiveSamlData($which = null)
	{
		$res = false;
		if ($which !== null) {
			$res = array_key_exists($which, $this->activeSamlData) ? $this->activeSamlData[$which] : false;
		} else {
			$res = $this->activeSamlData;
		}

		return $res;
	}

	public function isSamlAuthAllowed()
	{
		if (!empty($this->activeSamlData)) {
			return true;
		} else {
			return false;
		}
	}
}
