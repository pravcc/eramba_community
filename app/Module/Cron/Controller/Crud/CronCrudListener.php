<?php
App::uses('CrudListener', 'Crud.Controller/Crud');

/**
 * The Base Cron Listener
 *
 * @codeCoverageIgnore
 */
abstract class CronCrudListener extends CrudListener {

	/**
	 * Title for the CRON Listener to show as in results.
	 * 
	 * @var null|string
	 */
	protected $_title = null;

/**
 * Returns a list of all events that will fire in the controller during its life cycle.
 * You can override this function to add you own listener callbacks.
 *
 * - hourly: Called when hourly CRON job triggers by the system
 * - daily: Called when daily CRON job triggers by the system
 * - yearly: Called when yearly CRON job triggers by the system
 * - beforeHandle: Called right before any CRON job triggers by the system
 * - beforeJob: Called right after CRON job is successfully validated
 *               to startup all necessary classes and properties for execution
 * - afterJob: Called after all has been processed to clean the memory etc...
 *
 * @return array
 */
	public function implementedEvents() {
		$eventMap = array(
			'Cron.beforeHandle' => 'beforeHandle',
			'Cron.beforeJob' => 'beforeJob',
			
			'Cron.hourly' => 'hourly',
			'Cron.daily' => 'daily',
			'Cron.yearly' => 'yearly',

			'Cron.afterJob' => 'afterJob',
			'Cron.beforeRender' => 'beforeRender'
		);

		$events = array();
		foreach ($eventMap as $event => $method) {
			if (method_exists($this, $method)) {
				$events[$event] = $method;
			}
		}

		return $events;
	}

	/**
	 * BeforeHandle callback. 
	 * 
	 * @param  CakeEvent $event
	 * @return void
	 */
	public function beforeHandle(CakeEvent $event) {
	}

	/**
	 * Load component for current listener, includes a check if that component is not loaded already and
	 * triggers initialize and startup callback to make it act as if it was loaded automatically with controller.
	 * 
	 * @param  string   $component Component name, prefixed with plugin name if necessary
	 * @return Component           Instance of the component
	 */
	protected function _loadComponent($component) {
		$controller = $this->_controller();
		list(, $name) = pluginSplit($component);

		if (!$controller->{$name} instanceof Component) {
			// for tetsing to see what happens here, if this triggers sometime
			if ($controller->{$name} instanceof Component) {
				throw new CronException(__('Component %s has been already part of the controller but not loaded.', $component));
			}

			$controller->{$name} = $controller->Components->load($component);
			$controller->{$name}->initialize($controller);
			$controller->{$name}->startup($controller);
		}

		return $controller->{$name};
	}

	// get/set the title label
	public function title($title = null) {
		if ($title !== null) {
			$this->_title = $title;
		}

		return $this->_title;
	}

	/**
	 * Set error message for this listener.
	 * 
	 * @param string $message Message.
	 */
	public function setError($message) {
		$title = $this->config('title');
		$action = $this->_action();

		$message = sprintf('%s: %s', $title, $message);
		$action->setError($message);
	}

}
