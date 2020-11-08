<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('CakeEvent', 'Event');

class VisualisationCrudHelper extends CrudHelper
{
    public $helpers = ['Html', 'LimitlessTheme.ItemDropdown'];

    public function implementedEvents()
    {
        return [
            'ItemDropdown.beforeRender' => ['callable' => 'beforeItemDropdownRender', 'priority' => 20],
        ];
    }

    public function beforeItemDropdownRender(CakeEvent $event)
    {
        $Visualisation = $event->subject->view->get('Visualisation');

        if (!empty($Visualisation) && $Visualisation->isEnabled()) {
            $this->ItemDropdown->addItem(__('Share'), '#', [
                'icon' => 'cog',
                'data-yjs-request' => 'crud/showForm',
                'data-yjs-target' => 'modal',
                'data-yjs-datasource-url' => Router::url([
                    'plugin' => 'visualisation',
                    'controller' => 'visualisation',
                    'action' => 'share',
                    $event->data->getModel()->alias,
                    $event->data->getPrimary()
                ]),
                'data-yjs-event-on' => 'click',
            ]);
        }
    }
}
