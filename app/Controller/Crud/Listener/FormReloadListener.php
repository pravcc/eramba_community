<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('Router', 'Routing');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class FormReloadListener extends CrudListener
{
	const REQUEST_PARAM = 'formReload';
	const REQUEST_PARAM_FIELD = 'formReloadField';

	protected static $_isFormReload = false;

	protected static $_formReloadField = null;

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
	 * You can override this function to add you own listener callb>acks
	 *
	 * We attach at priority 50 so normal bound events can run before us
	 *
	 * @return array
	 */
	public function implementedEvents()
	{
		return [
			'Crud.beforeHandle' => ['callable' => 'beforeHandle', 'priority' => 10],
		];
	}

	public function beforeHandle(CakeEvent $event)
	{
		if (static::isFormReloadRequest($event->subject->request)) {
			$this->_adaptCrudAction($event);

			static::$_isFormReload = true;
			static::$_formReloadField = static::getFormReloadRequestField($event->subject->request);

			unset($event->subject->request->query[self::REQUEST_PARAM]);
			unset($event->subject->request->query[self::REQUEST_PARAM_FIELD]);
		}
	}

	public static function isFormReloadRequest(CakeRequest $request)
	{
		return isset($request->query[self::REQUEST_PARAM]) && $request->query[self::REQUEST_PARAM];
	}

	public static function getFormReloadRequestField(CakeRequest $request)
	{
		return isset($request->query[self::REQUEST_PARAM_FIELD]) ? $request->query[self::REQUEST_PARAM_FIELD] : null;
	}

	public static function isFormReload($field = null)
	{
		$fieldMatch = true;

		if ($field !== null) {
			$fieldMatch = static::isFormReloadField($field);
		}

		return static::$_isFormReload && $fieldMatch;
	}

	public static function getFormReloadField()
	{
		return static::$_formReloadField;
	}

	public static function isFormReloadField($field)
	{
		if ($field instanceof FieldDataEntity) {
			$field = $field->getFieldName();
		}

		return static::$_formReloadField == $field;
	}

	/**
	 * Adapt crud and events.
	 */
	protected function _adaptCrudAction(CakeEvent $event)
	{
		$this->_action()->config('saveAssociatedHandler', false);
		$this->_action()->config('saveMethod', 'formReloadSave');

		$this->_detachEvents([
			'Crud.beforeSave',
			'Crud.afterSave',
		]);

		//stop propagation of Crud.setFlash event
		$event->subject->crud->on('Crud.setFlash', function(CakeEvent $event) {
			$event->stopPropagation();
		}, ['priority' => 1]);
	}

	/**
	 * Detach selected events that we want to prevent.
	 */
	protected function _detachEvents($events = [])
	{
		$eventManager = $this->_controller()->getEventManager();

		foreach ($events as $event) {
			$listeners = $eventManager->listeners('Crud.beforeSave');

			foreach ($listeners as $listener) {
				$this->_controller()->getEventManager()->detach($listener, $event);
			}
		}
	}

}
