<?php
App::uses('Component', 'Controller');

class AjaxComponent extends Component
{
	public $components = [
		'Crud.Crud',
		'YoonityJSConnector.YoonityJSConnector',
		'Modals.Modals'
	];

	public $settings = [
	];

	protected $_useAjax = false;

	protected $_controller;

	public function __construct(ComponentCollection $collection, $settings = array())
	{
		if (empty($this->settings)) {
			$this->settings = [
			];
		}

		$settings = array_merge($this->settings, (array)$settings);
		parent::__construct($collection, $settings);
	}

	public function startup(Controller $controller)
	{
		unset($controller->request->params['isAjax']);
	}

	public function initialize(Controller $controller)
	{
		$this->_controller = $controller;

		if ($this->_controller->request->is('ajax')) {

			$this->_useAjax = true;

			$this->YoonityJSConnector->enable();

			$this->Crud->on('beforeRender', function(CakeEvent $event)
			{
				if ($this->_controller->request->is('post') || $this->_controller->request->is('put') || $this->_controller->request->is('delete')) {
					$this->YoonityJSConnector->setState($event->subject->success == true ? 'success' : 'error');
				}
			});
		}
	}

	public function beforeRender(Controller $controller)
	{
		if ($this->_useAjax) {
			if (!empty($this->_controller->request->query['modalId']) && $this->_controller->response->statusCode() != 200) {
				$modalHeading = __('An error has occurred');
				if ($this->_controller->response->statusCode() == 403) {
					$modalHeading = __('Access Denied');
				}
				$this->initModal('warning', $modalHeading);
				$this->setState('error');
			}

			$notification = null;
			if (CakeSession::check('Message.flash')) {
				$msgsTemp = CakeSession::read('Message.flash');
				foreach ($msgsTemp as $msg) {
					if (!isset($msg['message']) || !isset($msg['element'])) {
						continue;
					}
					
					$message = $msg['message'];
					$typeTemp = $msg['element'];

					$typesConvert = [
						'default' => 'info',
						'error' => 'error',
						'success' => 'success',
						'warning' => 'warning'
					];
					$typeTempExp = explode('/', $typeTemp);
					$type = end($typeTempExp);
					$this->addNotification($message, array_key_exists($type, $typesConvert) ? $typesConvert[$type] : 'info');
				}

				// Delete flash message from cache
				CakeSession::delete('Message.flash');
			}

			$modalInitialized = $this->Modals->initModal();
			if ($modalInitialized) {
				//
				// Set modal info
				$this->YoonityJSConnector->addData('modal', [
					'modalId' => $this->Modals->getModalId(),
					'friendlyName' => $this->Modals->getFriendlyName()
				]);
				//
			}
		}
	}

	public function setState($state)
	{
		$this->YoonityJSConnector->setState($state);
	}

	public function addNotification($message, $type)
	{
		$this->YoonityJSConnector->addNotification($message, $type);
	}

	public function initModal($type, $headerMsg = '')
	{
		$this->Modals->init();
		$this->Modals->setType($type);
		$this->Modals->setHeaderHeading($headerMsg);
	}
}
