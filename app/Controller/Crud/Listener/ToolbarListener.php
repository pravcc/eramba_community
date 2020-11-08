<?php
App::uses('CrudListener', 'Crud.Controller/Crud');

/**
 * Toolbar Listener
 */
class ToolbarListener extends CrudListener
{
	public function implementedEvents()
	{
		return [
			'Crud.beforeRender' => ['callable' => 'beforeRender', 'priority' => 999],
			'Crud.afterDelete' => ['callable' => 'afterDelete', 'priority' => 50],
			'Crud.afterRestore' => ['callable' => 'afterRestore', 'priority' => 50],
		];
	}

	public function beforeRender(CakeEvent $event)
	{
		if ($this->_isToolbarRequest($event->subject->request)) {
			$this->_controller()->layout = 'toolbar';
		}
	}

	protected function _isToolbarRequest(CakeRequest $request)
	{
		return (isset($request->query['toolbar']) && $request->query['toolbar'] && $request->is('ajax'));
	}

	public function afterDelete(CakeEvent $event)
	{
		if ($event->subject->success) {
			Cache::clear(false, 'trash_settings');
			Cache::clear(false, 'layout_toolbar');
		}
	}

	public function afterRestore(CakeEvent $event)
	{
		if ($event->subject->success) {
			Cache::clear(false, 'trash_settings');
			Cache::clear(false, 'layout_toolbar');
		}
	}
}
