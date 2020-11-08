<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');

class ImportToolCrudHelper extends CrudHelper
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
		$ImportTool = $this->_View->get('ImportTool');

		if ($ImportTool->enabled()) {
			$this->LayoutToolbar->addItem(__('Import'), '#', [
				'parent' => 'actions',
				'icon' => 'add-to-list',
				'data-yjs-request' => 'crud/showForm',
				'data-yjs-target' => 'modal',
				'data-yjs-event-on' => 'click',
				'data-yjs-modal-size-width' => '80',
				'data-yjs-datasource-url' =>  Router::url([
					'plugin' => 'importTool',
					'controller' => 'importTool',
					'action' => 'upload',
					$ImportTool->getSubject()->model->modelFullAlias()
				]),
			]);
		}
	}
}
