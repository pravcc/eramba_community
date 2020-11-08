<?php
/**
 * Event Manager Subject
 */
class EventManagerSubject {

/**
 * Instance of the event manager behavior.
 *
 * @var EventManagerBehavior
 */
	public $eventManager;


/**
 * Name of the default controller model class
 *
 * @var string
 */
	public $modelClass;

/**
 * The default action model instance
 *
 * @var Model
 */
	public $model;

/**
 * Optional arguments passed
 *
 * @var array
 */
	public $args;

/**
 * List of events this subject has passed through
 *
 * @var array
 */
	protected $_events = array();

/**
 * Constructor
 *
 * @param array $fields
 * @return void
 */
	public function __construct($fields = array()) {
		$this->set($fields);
	}

/**
 * Add an event name to the list of events this subject has passed through
 *
 * @param string $name name of event
 * @return void
 */
	public function addEvent($name) {
		$this->_events[] = $name;
	}

/**
 * Returns the list of events this subject has passed through
 *
 * @return array
 */
	public function getEvents() {
		return $this->_events;
	}

/**
 * Returns whether the specified event is in the list of events
 * this subject has passed through
 *
 * @param string $name name of event
 * @return array
 */
	public function hasEvent($name) {
		return in_array($name, $this->_events);
	}

/**
 * Set a list of key / values for this object
 *
 * @param array $fields
 * @return void
 */
	public function set($fields) {
		foreach ($fields as $k => $v) {
			$this->{$k} = $v;
		}
	}

}
