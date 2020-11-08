<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('CakeEvent', 'Event');

class SectionInfoCrudHelper extends CrudHelper
{
    public $helpers = ['Html', 'LimitlessTheme.LayoutToolbar'];

    public function implementedEvents()
    {
        return [
            'LayoutToolbar.beforeRender' => ['callable' => 'beforeLayoutToolbarRender', 'priority' => 90],
        ];
    }

    public function beforeLayoutToolbarRender(CakeEvent $event)
    {
        $Model = $event->subject->model;

        if (method_exists($Model, 'getSectionInfoConfig')) {
            $this->LayoutToolbar->addItem(__('Help'), '#', [
                'data-yjs-request' => 'crud/load',
                'data-yjs-target' => 'modal',
                'data-yjs-datasource-url' => Router::url(['plugin' => 'section_info', 'controller' => 'sectionInfo', 'action' => 'info', $Model->alias]),
                'data-yjs-event-on' => 'click',
                'data-yjs-modal-size-width' => 80
            ]);
        }
    }
}
