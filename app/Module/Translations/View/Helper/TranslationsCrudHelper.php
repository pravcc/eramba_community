<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');
App::uses('Translation', 'Translations.Model');

class TranslationsCrudHelper extends CrudHelper
{
	public $helpers = ['Html', 'LimitlessTheme.LayoutToolbar', 'LimitlessTheme.ItemDropdown'];

	public function implementedEvents()
    {
        return [
            'LayoutToolbar.beforeRender' => ['callable' => 'beforeLayoutToolbarRender', 'priority' => 50],
            'ItemDropdown.beforeRender' => ['callable' => 'beforeItemDropdownRender', 'priority' => 60],
        ];
    }

    public function beforeItemDropdownRender(CakeEvent $event)
    {
        $Item = $event->data;

        if ($Item->getPrimary() != Translation::DEFAULT_TRANSLATION_ID) {
            $url = Router::url([
                'plugin' => 'translations',
                'controller' => 'translations',
                'action' => 'download',
                $Item->getPrimary()
            ]);

            $this->ItemDropdown->addItem(__('Download PO File'), $url, [
                'icon' => 'file-download',
                'class' => 'white-space-normal'
            ]);
        }
    }

	public function beforeLayoutToolbarRender($event)
	{
		$this->LayoutToolbar->addItem(__('Download'), '#', [], [
			[
				__('Current Template (POT)'),
				[
					'plugin' => 'translations',
					'controller' => 'translations',
					'action' => 'downloadTemplate',
				],
				[
					'icon' => 'file-download',
				]
			]
		]);
	}
}
