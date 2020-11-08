<?php
App::uses('Hash', 'Utility');
App::uses('CakeTime', 'Utility');
App::uses('View', 'View');

class BaseAppNotification
{
	/**
	 * Custom params. [param_name => param_value]
	 * 
	 * @var array
	 */
	protected $_params = [];

	/**
	 * Notification title.
	 * 
	 * @var string
	 */
	protected $_title = null;

	/**
	 * Notification subject model.
	 * 
	 * @var string
	 */
	protected $_model = null;

	/**
	 * Notification subject id.
	 * 
	 * @var int
	 */
	protected $_foreignKey = null;

	/**
	 * Notification expiration datetime.
	 * 
	 * @var string Datetime Y-m-d H:i:s
	 */
	protected $_expiration = null;

	/**
	 * Notification datetime of create.
	 * 
	 * @var string Datetime Y-m-d H:i:s
	 */
	protected $_created = null;

	/**
	 * If notification is seen or not.
	 * 
	 * @var boolean
	 */
	protected $_seen = false;

	/**
	 * If redirect action is modal request.
	 * 
	 * @var boolean
	 */
	protected $_modalRequest = false;

	/**
	 * Create notification. Preset $Item data.
	 * 
	 * @param ItemDataEntity of AppNotification
	 */
	public function __construct($Item = null)
	{
		if ($Item !== null) {
			$this->setTitle($Item->title);
			$this->setModel($Item->model);
			$this->setForeignKey($Item->foreign_key);
			$this->setExpiration($Item->expiration);
			$this->setCreated($Item->created);
			
			if (!empty($Item->AppNotificationParam)) {
				foreach ($Item->AppNotificationParam as $Param) {
					$this->setParam($Param->key, $Param->value);
				}
			}
		}
	}

	/**
	 * Render AppNotification list item.
	 * 
	 * @param View $view
	 * @return string
	 */
	public function renderListItem(View $view)
	{
		$icon = $view->Html->div('media-left', $this->renderIcon($view));

		$dateTime = CakeTime::timeAgoInWords($this->getCreated(), [
			'accuracy' => [
				'hour' => 'hour'
			],
			'end' => '1 day',
			'format' => 'Y-m-d'
		]);

		$date = $view->Html->tag('span', $dateTime, [
			'class' => 'media-annotation'
		]);

		$body = $view->Html->div('media-body', $this->getTitle() . $date);

		$unseenClass = (!$this->isSeen()) ? 'unseen' : '';

		$requestAttr = [];

		if ($this->isModalRequest()) {
			$requestAttr = [
				'data-yjs-request' => 'crud/load',
				'data-yjs-target' => 'modal',
				'data-yjs-event-on' => 'click',
				'data-yjs-datasource-url' => $this->getRedirectUrl()
			];
		}
		else {
			$requestAttr = [
				'data-redirect-url' => $this->getRedirectUrl()
			];
		}

		return $view->Html->tag('li', $icon . $body, array_merge([
			'class' => 'media app-notification-item ' . $unseenClass,
			'escape' => false,
		], $requestAttr));
	}

	/**
	 * Render AppNotification icon.
	 * 
	 * @param View $view
	 * @return string
	 */
	public function renderIcon(View $view)
	{
		return $view->Html->tag('span', $view->Html->tag('i', '', ['class' => 'icon-info3']), [
			'class' => 'btn border-info text-info btn-flat btn-rounded btn-icon btn-sm app-notification-icon',
			'escape' => false
		]);
	}

	/**
	 * Link of notification redirect.
	 * 
	 * @return string
	 */
	public function getRedirectUrl()
	{
		return '#';
	}

	public function setTitle($title)
	{
		$this->_title = $title;
	}

	public function getTitle()
	{
		return $this->_title;
	}

	public function setModel($model)
	{
		$this->_model = $model;
	}

	public function getModel()
	{
		return $this->_model;
	}

	public function setForeignKey($foreignKey)
	{
		$this->_foreignKey = $foreignKey;
	}

	public function getForeignKey()
	{
		return $this->_foreignKey;
	}

	public function setExpiration($datetime)
	{
		$this->_expiration = date('Y-m-d H:i:s', strtotime($datetime));
	}

	public function getExpiration()
	{
		return $this->_expiration;
	}

	public function setCreated($datetime)
	{
		$this->_created = date('Y-m-d H:i:s', strtotime($datetime));
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function setParam($key, $value)
	{
		$this->_params[$key] = $value;
	}

	public function getParam($key)
	{
		return $this->_params[$key];
	}

	public function getParams()
	{
		return $this->_params;
	}

	public function setSeen($seen)
	{
		$this->_seen = $seen;
	}

	public function isSeen()
	{
		return $this->_seen;
	}

	public function isModalRequest()
	{
		return $this->_modalRequest;
	}
}
