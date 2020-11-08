<?php
App::uses('Component', 'Controller');
App::uses('ErambaHttpSocket', 'Network/Http');

class ErambaHttpComponent extends Component {
	private $statsUrl = STATS_REQUEST;
	public $config = array(
		'timeout' => 4,
		'ssl_verify_peer' => false
	);

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	/**
	 * Get app configured socket instance.
	 */
	protected function getSocket($config = array()) {
		if (empty($config)) {
			$config = $this->config;
		}
		
		$http = new ErambaHttpSocket($config);

		return $http;
	}

	public function registerClientID($clientId) { 
		$uri = $this->statsUrl . $clientId;
		$query = http_build_query(array(
			'app_version' => Configure::read('Eramba.version'),
			'db_version' => DB_SCHEMA_VERSION
		));

		$http = new ErambaHttpSocket($this->config);
		$http->blockingRequest = false;

		try {
			return $http->get($uri, $query);
		}
		catch (Exception $e) {
			return false;
		}

		return false;
	}

}
