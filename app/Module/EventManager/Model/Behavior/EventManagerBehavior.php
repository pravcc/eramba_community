<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');
App::uses('CakeEvent', 'Event');
App::uses('EventManagerSubject', 'EventManager.Model/EventManager');

/**
 * EventManagerBehavior
 */
class EventManagerBehavior extends ModelBehavior {

	/**
	 * Default config
	 *
	 * @var array
	 */
	protected $_defaults = [
		'enabled' => true,
		'eventPrefix' => null,
		'eventLogging' => true
	];

	public $settings = [];

	/**
	 * Reference to the current event manager.
	 *
	 * @var CakeEventManager
	 */
	protected $_eventManager;

	/**
	 * A flat array of the events triggered.
	 *
	 * @var array
	 */
	protected $_eventLog = [];

	/**
	 * Setup
	 */
	public function setup(Model $Model, $settings = array()) {
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);

			$this->_eventManager[$Model->alias] = $Model->getEventManager();
			$this->_eventLog[$Model->alias] = [];
		}
	}

	protected function _getEventName(Model $Model, $event) {
		if (!strpos($event, '.')) {
			$event = $this->settings[$Model->alias]['eventPrefix'] . '.' . $event;
		}

		return $event;
	}

	/**
	 * Attaches an event listener function to the model for working with Events.
	 *
	 * @param Model
	 * @param string|array $events Name of the Event you want to attach to model.
	 * @param callback $callback Callable method or closure to be executed on event.
	 * @param array $options Used to set the `priority` and `passParams` flags to the listener.
	 * @return void
	 */
	public function on(Model $Model, $events, $callback, $options = array()) {
		foreach ((array)$events as $event) {
			$event = $this->_getEventName($Model, $event);

			$this->_eventManager[$Model->alias]->attach($callback, $event, $options);
		}
	}

	/**
	 * Triggers a Model event by creating a new subject and filling it with $data,
	 * if $data is an instance of EventManagerSubject it will be reused as the subject
	 * object for this event.
	 *
	 * @param Model
	 * @param string $eventName
	 * @param array $data
	 * @return EventManagerSubject
	 */
	public function trigger(Model $Model, $eventName, $data = array()) {
		$eventName = $this->_getEventName($Model, $eventName);
		$subject = $data instanceof EventManagerSubject ? $data : $this->getSubject($Model, $data);
		$subject->addEvent($eventName);

		if (!empty($this->settings[$Model->alias]['eventLogging'])) {
			$this->_logEvent($Model, $eventName, $data);
		}

		$event = new CakeEvent($eventName, $subject);
		$this->_eventManager[$Model->alias]->dispatch($event);

		// if ($event->result instanceof CakeResponse) {
		// 	$exception = new Exception();
		// 	$exception->response = $event->result;
		// 	throw $exception;
		// }

		$subject->stopped = false;
		if ($event->isStopped()) {
			$subject->stopped = true;
		}

		return $subject;
	}

	/**
	 * Add a log entry for the event.
	 *
	 * @param Model
	 * @param string $eventName
	 * @param array $data
	 * @return void
	 */
	protected function _logEvent(Model $Model, $eventName, $data = array()) {
		$this->_eventLog[$Model->alias][] = array(
			$eventName,
			$data
		);
	}

	// get the event log
	public function eventLog($Model = null) {
		if ($Model instanceof Model) {
			return $this->_eventLog[$Model->alias];
		}

		return $this->_eventLog;
	}

	/**
	 * Create a CakeEvent subject with the required properties.
	 *
	 * @param array $additional Additional properties for the subject.
	 * @return EventManagerSubject
	 */
	public function getSubject(Model $Model, $additional = array()) {
		$subject = new EventManagerSubject();
		$subject->eventManager = $this;
		$subject->model = $Model;
		$subject->modelClass = $Model->alias;
		$subject->set($additional);

		return $subject;
	}


}
