<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('CustomLabelsView', 'CustomLabels.Controller/Crud/View');

/**
 * CustomLabels Listener
 */
class CustomLabelsListener extends CrudListener
{
	public function implementedEvents()
	{
		return array(
			'Crud.beforeRender' => array('callable' => 'beforeRender', 'priority' => 50),
		);
	}

	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $event
	 * @return void
	 */
	public function beforeRender(CakeEvent $event)
	{
		if (!empty($event->subject->action) && $event->subject->action == 'index') {
			$CustomLabelsView = new CustomLabelsView($event->subject);
			$this->_controller()->set('CustomLabels', $CustomLabelsView);
		}
	}
}
