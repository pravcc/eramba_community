<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');
App::uses('CakeEvent', 'Event');

class CompliancePackageRegulatorsCrudHelper extends CrudHelper
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
		$this->LayoutToolbar->addItem(__('Import'), '#', [
			'parent' => 'actions',
			'icon' => 'add-to-list',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			// 'data-yjs-modal-size-width' => '80',
			'data-yjs-datasource-url' =>  Router::url([
				'plugin' => null,
				'controller' => 'compliancePackages',
				'action' => 'import'
			]),
		]);

		$this->LayoutToolbar->addItem(__('Duplicate'), '#', [
			'parent' => 'actions',
			'icon' => 'copy3',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			// 'data-yjs-modal-size-width' => '80',
			'data-yjs-datasource-url' =>  Router::url([
				'plugin' => null,
				'controller' => 'compliancePackages',
				'action' => 'duplicate'
			]),
		]);
	}
}
