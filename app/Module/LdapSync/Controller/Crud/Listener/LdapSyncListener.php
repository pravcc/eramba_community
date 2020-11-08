<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('LdapSyncView', 'LdapSync.Controller/Crud/View');

/**
 * LdapSync Listener
 */
class LdapSyncListener extends CrudListener
{

	public function implementedEvents() {
		return array(
			'Crud.beforeRender' => array('callable' => 'beforeRender', 'priority' => 50)
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
		$this->_controller()->set('LdapSync', new LdapSyncView($e->subject));
	}

}
