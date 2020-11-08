<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('CakeEvent', 'Event');
App::uses('AwarenessProgram', 'Model');

class AwarenessProgramsCrudHelper extends CrudHelper
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
        $Model = $Item->getModel();

        $this->ItemDropdown->addItem(__('Demo'), '#', [
            'data-yjs-request' => 'crud/showForm',
            'data-yjs-target' => 'modal',
            'data-yjs-datasource-url' => Router::url([
                'plugin' => false,
                'controller' => 'awarenessPrograms',
                'action' => 'demo',
                $Item->getPrimary()
            ]),
            'data-yjs-event-on' => 'click',
            'data-yjs-on-modal-failure' => 'close',
            'data-yjs-modal-size-width' => 80,
            'data-yjs-modal-breadcrumbs' => $Model->label(),
        ]);

        $label = __('Stop');
        $action = 'stop';

        if ($Item->status == AwarenessProgram::STATUS_STOPPED) {
            $label = __('Start');
            $action = 'start';
        }

        $this->ItemDropdown->addItem($label, '#', [
            'data-yjs-request' => 'crud/load',
            'data-yjs-target' => 'modal',
            'data-yjs-datasource-url' => Router::url([
                'plugin' => false,
                'controller' => 'awarenessPrograms',
                'action' => $action,
                $Item->getPrimary()
            ]),
            'data-yjs-event-on' => 'click',
            'data-yjs-on-modal-failure' => 'close',
            'data-yjs-modal-size-width' => 80,
            'data-yjs-modal-breadcrumbs' => $Model->label(),
        ]);
    }
}
