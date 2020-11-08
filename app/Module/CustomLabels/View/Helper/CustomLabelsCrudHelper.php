<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');

class CustomLabelsCrudHelper extends CrudHelper
{
	public $helpers = ['Html', 'LimitlessTheme.LayoutToolbar'];

	public function implementedEvents()
    {
        return [
            'LayoutToolbar.beforeRender' => ['callable' => 'beforeLayoutToolbarRender', 'priority' => 50],
        ];
    }

	public function beforeLayoutToolbarRender($event)
	{
		$this->_setToolbar();
	}

	protected function _setToolbar()
	{
		$CustomLabels = $this->_View->get('CustomLabels');

		$Model = $CustomLabels->getSubject()->model;

		if (!$Model->Behaviors->enabled('CustomLabels.CustomLabels')) {
			return;
		}

		if (empty($this->LayoutToolbar->config('settings'))) {
			$this->LayoutToolbar->addItem(__('Settings'), '#', [
				'slug' => 'settings',
			]);
		}

		$this->LayoutToolbar->addItem(__('Custom Labels'), '#', [
			'slug' => 'CustomLabels',
			'parent' => 'settings',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-datasource-url' => Router::url(['plugin' => 'custom_labels', 'controller' => 'customLabels', 'action' => 'edit', $Model->modelFullName()]),
			'data-yjs-event-on' => 'click',
		]);
	}
}
