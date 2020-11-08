<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('TooltipsView', 'Tooltips.Controller/Crud/View');

/**
 * Tooltips Listener
 */
class TooltipsListener extends CrudListener
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
		$this->_controller()->set('modelAlias', $e->subject->model->alias);
		$this->_controller()->set('Tooltips', new TooltipsView($e->subject));
	}
}
