<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('CommunityView', 'Controller/Crud/View');

/**
 * Community Listener
 */
class CommunityListener extends CrudListener
{

	public function implementedEvents() {
		return array(
			'Crud.beforeRender' => array('callable' => 'beforeRender', 'priority' => 100)
		);
	}
	
	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeRender(CakeEvent $e)
	{
		$this->_controller()->set('Community', new CommunityView($e->subject));
	}

}
