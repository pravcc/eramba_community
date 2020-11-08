<?php
App::uses('CrudListener', 'Crud.Controller/Crud');

/**
 * SubSection listener that handles indexes for a certain parent object (foreign_key).
 */
class SettingsSubSectionListener extends CrudListener
{
	protected $_settings = [
	];

	public function implementedEvents() {
		return array(
			'Crud.beforeHandle' => array('callable' => 'beforeHandle', 'priority' => 50),
			'Crud.beforeFilterItems' => 'beforeFilterItems',
			'Crud.beforeFilter' => 'beforeFilter',
			'Crud.beforeRender' => array('callable' => 'beforeRender', 'priority' => 50),
		);
	}

	public function beforeHandle(CakeEvent $e)
	{
		$controller = $e->subject->controller;

		$controller->helpers[] = 'SettingsSubSection';
	}

	public function beforeFilterItems(CakeEvent $e)
	{
	}

	public function beforeFilter(CakeEvent $e)
	{
	}
	
	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeRender(CakeEvent $e)
	{
		$controller = $e->subject->controller;

		$controller->set('settingsAdditionalBreadcrumbs', [
			0 => [
				'name' => __('Settings'),
				'link' => Router::url([
					'controller' => 'Settings',
					'action' => 'index',
					'plugin' => false,
					'prefix' => false,
					'admin' => false
				]),
				'options' => [
					'prepend' => true
				]
			]
		]);
	}

}
