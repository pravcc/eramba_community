<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');

class AssetsCrudHelper extends CrudHelper
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

		$this->LayoutToolbar->addItem(__('Classifications'), '#', [
			'parent' => 'settings',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			'data-yjs-modal-size-width' => 80,
			'data-yjs-datasource-url' =>  Router::url([
				'controller' => 'assetClassifications',
				'action' => 'index'
			])
		]);

		$this->LayoutToolbar->addItem(__('Labels'), '#', [
			'parent' => 'settings',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			'data-yjs-modal-size-width' => 80,
			'data-yjs-datasource-url' =>  Router::url([
				'controller' => 'assetLabels',
				'action' => 'index'
			])
		]);

		$this->LayoutToolbar->addItem(__('Asset Types'), '#', [
			'parent' => 'settings',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			'data-yjs-modal-size-width' => 80,
			'data-yjs-datasource-url' =>  Router::url([
				'controller' => 'assetMediaTypes',
				'action' => 'index'
			])
		]);
	}
}
