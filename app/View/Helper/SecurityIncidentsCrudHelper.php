<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');
App::uses('CakeEvent', 'Event');

class SecurityIncidentsCrudHelper extends CrudHelper
{
	public $helpers = ['Html', 'LimitlessTheme.LayoutToolbar'];

	public function implementedEvents()
    {
        return [
            'LayoutToolbar.beforeRender' => ['callable' => 'beforeLayoutToolbarRender', 'priority' => 50],
        ];
    }

	public function beforeLayoutToolbarRender(CakeEvent $event)
	{
		$this->_setToolbar();
	}

	protected function _setToolbar()
	{
		if (empty($this->LayoutToolbar->config('settings'))) {
			$this->LayoutToolbar->addItem(__('Settings'), '#', [
				'slug' => 'settings',
			]);
		}

		$this->LayoutToolbar->addItem(__('Stages'), '#', [
			'parent' => 'settings',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			'data-yjs-modal-size-width' => 80,
			'data-yjs-datasource-url' =>  Router::url([
				'controller' => 'securityIncidentStages',
				'action' => 'index'
			])
		]);
	}
}
