<?php

App::uses('Component', 'Controller');
App::uses('CakeSession', 'Model/Datasource');

class YoonityJSConnectorComponent extends Component
{
	public $components = [];

	protected $_controller;

	protected $_allowed = true;

	protected $_enabled = false;

	protected $_state = 'success';

	protected $_notifications = [];

	protected $_requestedAction = null;

	protected $_content = null;

	protected $_data = [];

    public function __construct(ComponentCollection $collection, $settings = array())
    {
		if (empty($this->settings)) {
			$this->settings = [
			];
		}

		$settings = array_merge($this->settings, (array)$settings);
		parent::__construct($collection, $settings);
	}

	public function initialize(Controller $controller)
	{
		$this->_controller = $controller;
		$this->_requestedAction = $this->_controller->request->params['action'];
	}

	public function allow()
	{
		$this->_allowed = true;
	}

	public function deny()
	{
		$this->_allowed = false;
	}

	public function isAllowed()
	{
		return $this->_allowed;
	}

	public function enable()
	{
		$this->_enabled = true;
	}

	public function disable()
	{
		$this->_enabled = false;
	}

	public function isEnabled()
	{
		return $this->_enabled;
	}

	public function beforeRender(Controller $controller)
	{
		if ($this->isAllowed() && $this->isEnabled()) {
			$this->_controller->autoLayout = true;
			$this->_controller->autoRender = false;
		}
	}

	public function shutdown(Controller $controller)
	{
		if ($this->isAllowed() && $this->isEnabled()) {
			$this->send();
		}
	}

	protected function _getState()
	{
		return $this->_state;
	}

	protected function _getNotifications()
	{
		return $this->_notifications;
	}

	protected function _getRequestedAction()
	{
		return $this->_requestedAction;
	}

	protected function _getContent()
	{
		if ($this->_content === null) {
			$content = $this->_controller->response;
			return $content->body();
		} else {
			return $this->_content;
		}
	}

	public function setState($state)
	{
		if ($state === 'success' || $state === 'error' || $state == null) {
			$this->_state = $state;
		}
	}

	public function addNotification($message, $type)
	{
		$defaultType = 'info';
		$allowedTypes = ['success', 'error', 'info', 'warning'];
		$this->_notifications[] = [
			'message' => $message,
			'type' => in_array($type, $allowedTypes) ? $type : $defaultType
		];
	}

	public function setRequestedAction($action)
	{
		$this->_requestedAction = $action;
	}

	public function setContent($content)
	{
		$this->_content = $content;
	}

	public function addData($key, $val)
	{
		$this->_data[$key] = $val;
	}

	public function removeData($key)
	{
		unset($this->_data[$key]);
	}

	public function send()
	{
		$jsonData = [
			'state' => $this->_getState(),
			'notifications' => $this->_getNotifications(),
			'statusCode' => $this->_controller->response->statusCode(),
			'requestedAction' => $this->_getRequestedAction(),
			'content' => $this->_getContent(),
		];

		foreach ($this->_data as $key => $val) {
			$jsonData[$key] = $val;
		}

		echo json_encode($jsonData);

		$this->_controller->_stop();
	}
}