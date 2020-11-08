<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('Router', 'Routing');

class QuickAddListener extends CrudListener
{
	const REQUEST_PARAM = 'quickAdd';
	const REQUEST_PARAM_ON_SUCCESS = 'onSuccess';

	/**
	 * Returns a list of all events that will fire in the controller during its lifecycle.
	 * You can override this function to add you own listener callb>acks
	 *
	 * We attach at priority 50 so normal bound events can run before us
	 *
	 * @return array
	 */
	public function implementedEvents()
	{
		return [
			'Crud.beforeRender' => ['callable' => 'beforeRender', 'priority' => 50],
		];
	}

	protected function _isQuickAddRequest(CakeRequest $request)
	{
		return isset($request->query[self::REQUEST_PARAM]) && $request->query[self::REQUEST_PARAM];
	}

	public function beforeRender(CakeEvent $event)
	{
		if ($this->_isQuickAddRequest($event->subject->request)) {
			$this->_controller()->Modals->setHeaderHeading(__('Quick Add Item') . ' (' . $this->_model()->label() . ')');
			$this->_controller()->Modals->changeConfig('footer.buttons.saveBtn.options.data-yjs-on-success-reload', $event->subject->request->query[self::REQUEST_PARAM_ON_SUCCESS]);
		}
	}

}
