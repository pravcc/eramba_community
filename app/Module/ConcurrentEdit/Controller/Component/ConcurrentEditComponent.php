<?php

App::uses('Component', 'Controller');

class ConcurrentEditComponent extends Component
{
	public $components = [
		'Crud.Crud',
		'Auth',
		'Modals.Modals',
		'YoonityJSConnector.YoonityJSConnector'
	];
	public $settings = [
		'actions' => [
			'edit'
		]
	];

	/**
	 * Model class of current controller
	 */
	public $model = null;
	/**
	 * Foreign Key of current edited item
	 */
	public $id = null;

	/**
	 * Runtime configuration values.
	 * 
	 * @var array
	 */
	protected $_runtime = [];

	/**
	 * Reference to the current event manager.
	 *
	 * @var CakeEventManager
	 */
	protected $_eventManager;

	public function __construct(ComponentCollection $collection, $settings = array())
	{
		if (empty($this->settings)) {
			$this->settings = array();
		}

		$settings = array_merge($this->settings, (array)$settings);
		parent::__construct($collection, $settings);

		$this->_runtime = $this->settings;
	}

	public function initialize(Controller $controller)
	{
		$this->controller = $controller;
		$this->model = $this->controller->{$this->controller->modelClass};
		$this->_eventManager = $this->controller->getEventManager();

		$this->ConcurrentEdit = ClassRegistry::init('ConcurrentEdit.ConcurrentEdit');
	}

	public function startup(Controller $controller)
	{
		$action = $this->controller->request->action;
		$this->id = isset($this->controller->request->params['pass'][0]) ? $this->controller->request->params['pass'][0] : false;
		if (in_array($action, $this->settings['actions']) && $this->id !== false) {
			$this->model = $this->controller->modelClass;
			$userId = $this->Auth->user('id');
			$currentEditingUser = $this->ConcurrentEdit->getCurrentLockedRecord($this->model, $this->id, null, 14);
			if (empty($currentEditingUser) || $currentEditingUser['User']['id'] == $userId) {
				$this->controller->set('ceModel', $this->model);
				$this->controller->set('ceForeignKey', $this->id);

				// Set CE Record for current user
				$this->ConcurrentEdit->setRecord($this->model, $this->id, $userId);

				//
				// Set CRUD callback for removing CE record after successfull save
				$this->Crud->on('beforeRender', function(CakeEvent $event)
				{
					if (($this->controller->request->is('post') || $this->controller->request->is('put')) && $event->subject->success == true) {
						$this->ConcurrentEdit->removeRecords($this->model, $this->id);
					}
				});
				//
			} else {
				$username = $currentEditingUser['User']['name'] . ' ' . $currentEditingUser['User']['surname'];
				$this->Modals->init();
				$this->Modals->setLayout('LimitlessTheme.modals/modal_warning');
				$this->controller->set('warningHeading', __('Item is being used by someone else!'));
				$this->controller->set('warningMessage', __('You can’t edit or delete this object as its being used by someone else (' . $username . '), you’ll need to wait until the other user finishes its work.'));
				$this->controller->set('warningButton', __('All right - I’ll wait'));
				$this->controller->render(false);
				$this->YoonityJSConnector->send();
			}
		}
	}
}