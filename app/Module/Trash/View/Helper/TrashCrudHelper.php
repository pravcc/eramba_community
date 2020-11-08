<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');

class TrashCrudHelper extends CrudHelper
{
	public $helpers = ['Html', 'LimitlessTheme.LayoutToolbar', 'LimitlessTheme.ItemDropdown'];

	public function implementedEvents()
    {
        return [
            'LayoutToolbar.beforeRender' => ['callable' => 'beforeLayoutToolbarRender', 'priority' => 50],
        ];
    }

	public function beforeLayoutToolbarRender($event)
	{
		$Trash = $this->_View->get('Trash');

		if ($Trash->isEnabled() && $Trash->hasItems() && !$Trash->isTrash()) {
			$this->LayoutToolbar->addItem(
				__('Trash'),
				[
					'action' => 'trash'
				],
				[
					// 'icon' => 'bin2',
					'notification' => $Trash->getCount()
				]
			);
		}
	}
}
