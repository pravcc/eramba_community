<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('CakeEvent', 'Event');

class GoalAuditsCrudHelper extends CrudHelper
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
        $Improvement = $event->data->GoalAuditImprovement;

        $url = [
            'plugin' => false,
            'controller' => 'goalAuditImprovements',
            'action' => 'add',
            $event->data->getPrimary()
        ];

        if (!empty($Improvement)) {
            $url = [
                'plugin' => null,
                'controller' => 'goalAuditImprovements',
                'action' => 'edit',
                $Improvement->getPrimary()
            ];
        }

        $this->ItemDropdown->addItem(__('Improve'), '#', [
            'data-yjs-request' => 'crud/showForm',
            'data-yjs-target' => 'modal',
            'data-yjs-datasource-url' => Router::url($url),
            'data-yjs-event-on' => 'click',
        ]);
    }
}
