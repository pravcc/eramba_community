<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('DashboardView', 'Dashboard.Controller/Crud/View');

/**
 * Dashboard Listener
 */
class DashboardListener extends CrudListener
{

	public function implementedEvents() {
		return [
			'Crud.beforeRender' => array('callable' => 'beforeRender', 'priority' => 50)
		];
	}
	
	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeRender(CakeEvent $e)
	{
		$this->_controller()->set('Dashboard', new DashboardView($e->subject));
	}

}
