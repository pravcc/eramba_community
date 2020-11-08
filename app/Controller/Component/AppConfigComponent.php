<?php
App::uses('Component', 'Controller');
App::uses('Configure', 'Core');
App::uses('Router', 'Routing');

class AppConfigComponent extends Component
{
	protected $_defaults = [];

	public function initialize(Controller $controller)
	{
		$this->controller = $controller;
	}

	public function ensureOffloadSSL()
	{
		if (Configure::read('Eramba.Settings.SSL_OFFLOAD_ENABLED') == 0) {
			return false;
		}
		
		if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
			$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' &&
			!$this->controller->request->is('ssl')) {
			$_SERVER['HTTPS'] = 'on';
			Router::fullBaseUrl(str_replace('http://', 'https://', Router::fullBaseUrl()));
		}
	}
}