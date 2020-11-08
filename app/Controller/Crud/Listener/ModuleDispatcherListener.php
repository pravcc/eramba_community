<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('AppModule', 'Lib');

/**

 */
class ModuleDispatcherListener extends CrudListener
{

/**
 * Default configuration
 *
 * @var array
 */
	protected $_settings = [
		'listeners' => null
	];

/**
 * Constructor
 *
 * @param CrudSubject $subject
 * @param array $defaults Default settings
 * @return void
 */
	public function __construct(CrudSubject $subject, $defaults = array()) {
		$defaults = am([
		], $defaults);

		parent::__construct($subject, $defaults);
	}

	public function implementedEvents() {
		return array(
			'Crud.initialize' => 'initialize'
		);
	}

	/**
	 * This callback checks if given module is available in the application, and if yes
	 * it tries to initialize the module's Listener into the current controller.
	 * Helpful if some modules are meant to be easily turned off only by removing them entirely.
	 * 
	 * @param  CakeEvent $e 
	 */
	public function initialize(CakeEvent $e)
	{
		$listeners = $this->config('listeners');
		if (!is_array($listeners)) {
			$listeners = (array) $listeners;
		}

		$listeners = Hash::normalize($listeners);
		foreach ($listeners as $name => $config) {
			list($plugin, $className) = pluginSplit($name);

			if (in_array($plugin, AppModule::getEnabledModules())) {
				if (!is_array($config)) {
					$config = (array) $config;
				}

				$e->subject->crud->addListener($className, $name, $config);
			}
		}
	}

}
