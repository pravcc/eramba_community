<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('CakeEvent', 'Event');

class DataAssetInstancesCrudHelper extends CrudHelper
{
    public $helpers = ['Html', 'LimitlessTheme.ItemDropdown'];

    public function implementedEvents()
    {
        return [
            'ItemDropdown.beforeRender' => ['callable' => 'beforeItemDropdownRender', 'priority' => 60],
        ];
    }

    public function beforeItemDropdownRender(CakeEvent $event)
    {
        $Item = $event->data;

        if (!$Item->isFlowsEnabled()) {
            $url = [
                'controller' => 'dataAssetSettings',
                'action' => 'add',
                $Item->getPrimary()
            ];
        }
        else {
            $url = [
                'controller' => 'dataAssetSettings',
                'action' => 'edit',
                $Item->DataAssetSetting->getPrimary()
            ];
        }

        $this->ItemDropdown->addItem(__('General Attributes'), '#', [
            'data-yjs-request' => 'crud/showForm',
            'data-yjs-target' => 'modal',
            'data-yjs-datasource-url' => Router::url($url),
            'data-yjs-event-on' => 'click',
        ]);
    }
}
