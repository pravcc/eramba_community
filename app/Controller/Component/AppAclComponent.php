<?php
App::uses('Component', 'Controller');
App::uses('AclComponent', 'Controller/Component');
App::uses('AclRouter', 'Acl.Lib');
App::uses('CakeLog', 'Log');

/**
 * Application helper class for easier ACL management.
 */
class AppAclComponent extends Component {
	public $components = array('Acl');
	public $settings = [];

	/**
	 * Runtime configuration.
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

	public function initialize(Controller $controller) {
		$this->controller = $controller;
		$this->_eventManager = $this->controller->getEventManager();

		// default requestor model
		// $this->requestor('User');
	}

	public function startup(Controller $controller) {
		$this->controller = $controller;
	}

	// defined a new events for this class
	public function implementedEvents() {
		return parent::implementedEvents() + [
			'Acl.afterCheck' => array('callable' => 'afterCheck', 'passParams' => true)
		];
	}

	/**
	 * Call an ACL action on object's permissions.
	 * 
	 * @param  array $requestor  Aro - requestor for permission, null for currently logged in user. [className, id]
	 * @param  string|array $object     Aco - controlled object for a permission action. [className, id]
	 * @param  string 		$action     Action type.
	 * @return boolean                  True if action successful, False otherwise.
	 */
	public function object($type = 'check', $requestor, $object, $action = '*') {
		// $requestor = $this->normalizeRequestor($requestor);

		$requestor = self::normalizeNode($requestor);
		$object = self::normalizeNode($object);
		
		$result = $this->Acl->{$type}($requestor, $object, $action);

		// if ($type == 'check') {
		// 	$result = $this->trigger(
		// 		'afterCheck',
		// 		[$result, $requestor, $object, $action],

		// 		// [default value]; allow break; beak on true
		// 		['arg' => 0, 'onValue' => null], true, true
		// 	);
		// }

		return $result;
	}

	/**
	 * After Acl->check() permission callback.
	 * 
	 * @param  bool $result    Original ACL check result.
	 * @param  array $requestor Aro - requestor for the check.
	 * @param  array $object    Aco - requested access to object.
	 * @param  mixed $action    Type of action that is being checked.
	 * @return mixed            Null to dont modify the result, True to allow access, False to deny it
	 */
	public function afterCheck($result, $requestor, $object, $action) {
		return null;
	}

	// /**
	//  * Attaches an event listener function to the controller.
	//  */
	// public function on($events, $callback, $options = array()) {
	// 	foreach ((array)$events as $event) {
	// 		if (!strpos($event, '.')) {
	// 			$event = 'Acl' . '.' . $event;
	// 		}

	// 		$this->_eventManager->attach($callback, $event, $options);
	// 	}
	// }

	// // trigger event
	// public function trigger($event, $args = array(), $default = ['arg' => 0, 'onValue' => true], $break = true, $breakOn = [false, null]) {
	// 	if (!$event instanceof CakeEvent) {
	// 		$event = new CakeEvent('Acl.' . $event, $this, $args);
	// 		list($event->break, $event->breakOn, $event->modParams) = array($break, $breakOn, 0);
	// 	}

	// 	$this->_eventManager->dispatch($event);
	// 	if ($event->isStopped()) {
	// 		return false;
	// 	}

	// 	$defaultData = isset($event->data[$default['arg']]) ? $event->data[$default['arg']] : true;
	// 	return $event->result === $default['onValue'] ? $defaultData : $event->result;
	// }

	/**
	 * Normalizes node array for ACL compatible use.
	 * 
	 * @param  mixed $node  Node.
	 * @return Normalized array node.
	 */
	public static function normalizeNode($node) {
		if (is_array($node)) {
			$node = array_values($node);
			$primary = 'id';

			if (!is_object($node[0])) {
				$node[0] = ClassRegistry::init($node[0]);
			}

			$node[0]->id = $node[1];
			return $node[0];
		}

		return $node;
	}

	/**
	 * Normalizes provided Aro - requestor for use with ACL. Input should look as [$model, $foreignKey]
	 * 
	 * @param  mixed $requestor  Null to use the currently logged user, array for custom node or user ID to get user's node.
	 * @return array 			 Parameter to get Aro node.
	 */
	// public function normalizeRequestor($requestor) {
	// 	debug($requestor);
	// 	if (is_array($requestor)) {
	// 		// if user id not set, use logged user id
	// 		if ($requestor[1] === null) {
	// 			$requestor[1] = $this->controller->logged['id'];
	// 		}
	// 	}

	// 	if (!is_array($requestor)) {
	// 		if (!is_object($requestor)) {
	// 			$requestor = ['User', $requestor];
	// 		}

			
	// 	}
	// 	// if requestor not provided or null use logged user id
	// 	if ($requestor[1] === null) {
	// 		$requestor[1] = $this->controller->logged['id'];
	// 	}


	// 	return $requestor;

	// 	// use currently logged user
	// 	// if ($requestor === null) {
	// 	// 	return self::normalizeNode([$this->requestor(), $this->controller->logged['id']]);
	// 	// }

	// 	// // only ID of the user provided
	// 	// if (!is_array($requestor)) {
	// 	// 	return self::normalizeNode([$this->requestor(), $requestor]);
	// 	// }

	// 	// return self::normalizeNode($requestor);
	// }

	/**
	 * Get or set a class that should be used as requestor (Aro) for object permission checks.
	 * 
	 * @param  string $className Class name (i.e Plugin.ClassName)
	 * @return void|Model
	 */
	// public function requestor($className = null) {
	// 	if ($className === null) {
	// 		return $this->_runtime['requestor'];
	// 	}

	// 	$this->controller->loadModel($className);
	// 	list( ,$name) = pluginSplit($className);
	// 	$this->_runtime['requestor'] = $this->controller->{$name};
	// }

	/**
	 * ACL permission check for controller's actions method mostly used in the app.
	 * 
	 * @param  mixed    $url     Router::url() compatible url.
	 *                           Example: /posts/index or array('controller' => 'posts', 'action' => 'index')
	 * @param  int|null $groupId Optionally check any permission for any group, defaults to logged in user's group.
	 * 
	 * @return boolean           Status of the permission.
	 */
	public function check($url, $groups = null) {
		if ($groups === null) {
			// in case this check is executed before $logged variable is even set in the controller
			if (count($this->controller->logged['Groups']) == 0) {
				CakeLog::write('notice', "AppAcl::check() called even before logged variable was set. \n" . Debugger::trace());
				return false;
			}

			$groups = $this->controller->logged['Groups'];
		}
		
		return $this->Acl->check(array(
			'Group' => array(
				'id' => $groups
			)),
			AclRouter::aco_path($url)
		);
	}
}