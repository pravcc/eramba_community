<?php
App::uses('Hash', 'Utility');

class AppNotificationBuilder
{
	/**
	 * Notification instance.
	 * 
	 * @var BaseAppNotification
	 */
	protected $_instance = null;

	/**
	 * Notification instance class name with plugin prefix.
	 * 
	 * @var string
	 */
	protected $_className = null;

	/**
	 * Construnct builder with BaseAppNotification instance.
	 * 
	 * @param string $className BaseAppNotification class name.
	 */
	public function __construct($className)
	{
		list($plugin, $name) = pluginSplit($className);

		if (!empty($plugin)) {
			$plugin = $plugin . '.';
		}

		App::uses($name, $plugin . 'Lib/AppNotification');

		$this->_instance = new $name();
		$this->_className = $className;
	}

	/**
	 * Call methods on $_instance.
	 * 
	 * @param string $name
	 * @param mixed $params
	 * @return AppNotificationBuilder
	 */
	public function __call($name, $params)
	{
		call_user_func_array([$this->_instance, $name], $params);

		return $this;
	}

	/**
	 * Get notification instance.
	 * 
	 * @return BaseAppNotification
	 */
	public function getInstance()
	{
		return $this->_instance();
	}

	/**
	 * Save current notification instance to DB.
	 * 
	 * @return boolean
	 */
	public function save()
	{
		$data = [
			'notification' => $this->_className,
			'title' => $this->_instance->getTitle(),
			'model' => $this->_instance->getModel(),
			'foreign_key' => $this->_instance->getForeignKey(),
			'expiration' => $this->_instance->getExpiration(),
		];

		$params = $this->_instance->getParams();

		return ClassRegistry::init('AppNotification.AppNotification')->saveAppNotification($data, $params);
	}
}