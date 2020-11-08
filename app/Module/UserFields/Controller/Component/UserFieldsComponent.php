<?php

App::uses('Component', 'Controller');
App::uses('Hash', 'Utility');
App::uses('ClassRegistry', 'Utility');

class UserFieldsComponent extends Component
{
	public $components = ['Crud'];
	public $settings = [
		'fields' => []
	];

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

		$this->init();
	}

	/**
	 * Initialize all fields, add related associations to contain of beforePaginate function of Crud
	 */
	public function init()
	{
		if (!empty($this->settings['fields'])) {
			$this->Crud->on('beforePaginate', function(CakeEvent $event) {
				$model = $this->Crud->getSubject()->model;
				if ($model->Behaviors->enabled('UserFields')) {
					$associations = [];
					foreach ($this->settings['fields'] as $field) {
						$oldContain = isset($event->subject->paginator->settings['contain']) ? $event->subject->paginator->settings['contain'] : [];
						$event->subject->paginator->settings['contain'] = $this->attachFieldsToArray($field, $oldContain);
					}
				}
			});
		}
	}

	/**
	 * Attach fields with their group fields (associations) to given data and return merged array
	 * @param  array $fields Fields from UserFields module
	 * @param  array $data   Data where fields will be attached
	 * @return array         Merged given data with attached fields
	 */
	public function attachFieldsToArray($fields, $data, $modelAlias = null)
	{
		$model = $this->model;
		if (!empty($modelAlias)) {
			$model = ClassRegistry::init($modelAlias);
		}

		if ($model->Behaviors->enabled('UserFields')) {
			foreach ((array)$fields as $field) {
				$data = Hash::merge($data, $model->Behaviors->UserFields->getAssociationsByField($model, $field));
			}
		}

		return $data;
	}

	/**
	 * Pass-throught method for UserFieldsBehavior's method
	 */
	public function getUserFieldUsers($modelAlias, $field, $ids = [], $userDbFields = [])
	{
		$model = $this->model;
		if ($modelAlias !== $this->model->alias) {
			$model = ClassRegistry::init($modelAlias);
		}

		if ($model->Behaviors->enabled('UserFields')) {
			return $model->Behaviors->UserFields->getUserFieldUsers($model, $field, $ids, $userDbFields);
		}

		return array();
	}
}