<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('CakeEvent', 'Event');

class WidgetCrudHelper extends CrudHelper
{
    public $helpers = ['Html', 'LimitlessTheme.ItemDropdown', 'Widget.Widget'];

    public function implementedEvents()
    {
        return [
            'ItemDropdown.beforeRender' => ['callable' => 'beforeItemDropdownRender', 'priority' => 30],
        ];
    }

    public function beforeItemDropdownRender(CakeEvent $event)
    {
        $Item = $event->data;
        $Model = $Item->getModel();

        $excludeWidget = [
            'Queue'
        ];

        $widget = true;

        if (($Item->getModel() instanceof SystemLog)
            || in_array($Model->alias, $excludeWidget)
        ) {
            $widget = false;
        }

        if ($widget) {
            $commentsCount = $this->Widget->getCommentsBadge($Item);
            $attachmentsCount = $this->Widget->getAttachmentsBadge($Item);

            $this->ItemDropdown->addItem(__('Comments %s & Attachments %s', $commentsCount, $attachmentsCount), '#', [
                'icon' => 'comment',
                'data-yjs-request' => 'crud/showForm',
                'data-yjs-target' => 'modal',
                'data-yjs-datasource-url' => Router::url([
                    'plugin' => 'widget',
                    'controller' => 'widget',
                    'action' => 'index',
                    $Model->modelFullName(),
                    $Item->getPrimary()
                ]),
                'data-yjs-event-on' => 'click',
            ]);
        }
    }
}
