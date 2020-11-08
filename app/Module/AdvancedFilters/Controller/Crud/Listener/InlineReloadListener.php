<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('Router', 'Routing');

class InlineReloadListener extends CrudListener
{

	/**
	 * Constructor
	 *
	 * @param CrudSubject $subject
	 * @param array $defaults Default settings
	 * @return void
	 */
	public function __construct(CrudSubject $subject, $defaults = array())
	{
		parent::__construct($subject, $defaults);
	}

	/**
	 * Returns a list of all events that will fire in the controller during its lifecycle.
	 * You can override this function to add you own listener callbacks
	 *
	 * We attach at priority 50 so normal bound events can run before us
	 *
	 * @return array
	 */
	public function implementedEvents()
	{
		return [
			'Crud.beforeFilter' => ['callable' => 'beforeFilter', 'priority' => 50],
			'Crud.beforeRender' => ['callable' => 'beforeRender', 'priority' => 999],
			'AdvancedFilter.beforeFind' => ['callable' => 'beforeFind', 'priority' => 50],
		];
	}

	public function beforeFilter(CakeEvent $event)
	{
		if (!empty($event->subject->AdvancedFiltersObject)) {
			// attach an event to the advanced filter object to additionally make changes to the final query
			$event->subject->AdvancedFiltersObject->getEventManager()->attach($this);
		}
	}

	public function beforeRender(CakeEvent $event)
	{
		if ($this->_isInlineReloadRequest($event->subject->request)) {
			$this->_controller()->Modals->init(false);
			$this->_controller()->layout = 'clean';
			$event->subject->crud->action()->view('AdvancedFilters./Elements/inline_reload');
		}
	}

	public function beforeFind(CakeEvent $event)
	{
		$request = Router::getRequest();

		if ($this->_isInlineReloadRequest($request)) {
			$Model = $event->subject->getModel();

			$event->data[0]['conditions']["{$Model->alias}.id"] = $request->query['inlineReload'];
		}
	}

	protected function _isInlineReloadRequest(CakeRequest $request)
	{
		return isset($request->query['inlineReload']) && $request->query['inlineReload']; //&& $request->is('ajax');
	}

}
