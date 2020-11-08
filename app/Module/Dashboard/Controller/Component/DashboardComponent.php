<?php
App::uses('Component', 'Controller');

/**
 * Dashboard component.
 */
class DashboardComponent extends Component {

	/**
	 * Reference to the current event manager.
	 *
	 * @var CakeEventManager
	 */
	protected $_eventManager;

	public function __construct(ComponentCollection $collection, $settings = array()) {
		if (empty($this->settings)) {
			$this->settings = array(
			);
		}

		$settings = array_merge($this->settings, (array)$settings);
		parent::__construct($collection, $settings);

		$this->_runtime = $this->settings;
	}

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	public function getKpi() {
		$event = new CakeEvent('Visualisation.onCheck', $this, array($requestor, $model, $object, $action));
		list($event->break, $event->breakOn) = array(true, true);
		$this->_eventManager->dispatch($event);
		if ($event->result === true) {
			return true;
		}

		return false;
	}

	public function check($requestor, Model $model, $object, $action) {
		
	}

	/**
	 * Validates VisualisationUser access to $object.
	 * Returns True or nothing which is NULL, because False would stop event dispatcher.
	 */
	public function onCheck($requestor, Model $model, $object, $action) {
	}

	// defined a new events for this class
	public function implementedEvents() {
		return parent::implementedEvents() + [
			'Visualisation.onCheck' => array('callable' => 'onCheck', 'passParams' => true)
		];
	}

}
