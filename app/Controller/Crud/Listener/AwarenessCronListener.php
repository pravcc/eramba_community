<?php
App::uses('CronCrudListener', 'Cron.Controller/Crud');
App::uses('CronException', 'Cron.Error');
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('AwarenessMgtComponent', 'Controller/Component');

/**
 * Awareness CRON listener.
 */
class AwarenessCronListener extends CronCrudListener
{
	public function beforeHandle(CakeEvent $event)
	{
		$this->_buildComponent();
	}

	/**
	 * Temporary solution until we get rid of LdapConnectorsMgtComponent class.
	 * Builds a standalone Component class that handles awareness cron functionality.
	 * 
	 * @return Component
	 */
	protected function _buildComponent()
	{
		$controller = new Controller(new CakeRequest());
		$collection = new ComponentCollection();

		$this->AwarenessMgt = new AwarenessMgtComponent($collection);
		$this->AwarenessMgt->initialize($controller);
		$this->AwarenessMgt->startup($controller);
	}

	public function daily(CakeEvent $event)
	{
		if (!$this->AwarenessMgt->cron()) {
			throw new CronException(__('Awareness Program processing failed'));
		}
	}

}
