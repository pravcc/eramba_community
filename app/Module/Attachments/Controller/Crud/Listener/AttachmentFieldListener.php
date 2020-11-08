<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('WidgetListener', 'Widget.Controller/Crud/Listener');

/**
 * Attachments Field Listener
 */
class AttachmentFieldListener extends CrudListener
{
    /**
     * Default configuration
     *
     * @var array
     */
    protected $_settings = [];

    /**
     * Returns a list of all events that will fire in the controller during its lifecycle.
     * You can override this function to add you own listener callbacks
     *
     * We attach at priority 50 so normal bound events can run before us
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Crud.beforeHandle' => ['callable' => 'beforeHandle', 'priority' => 50],
            'Crud.beforeRender' => ['callable' => 'beforeRender', 'priority' => 50],
            'Crud.afterSave' => ['callable' => 'afterSave', 'priority' => 50]
        ];
    }

    public function beforeHandle(CakeEvent $event)
    {
    }

    public function afterSave(CakeEvent $event)
    {
        $Session = $event->subject->controller->Session;

        $hash = $Session->read("{$event->subject->model->alias}.Attachment.hash");

        if ($event->subject->success && $event->subject->created && !empty($hash)) {
            ClassRegistry::init('Attachments.Attachment')->tmpToNormal(
                $hash,
                $event->subject->model->modelFullName(),
                $event->subject->id
            );

            $this->_widgetView($event->subject->model, $event->subject->id);
        }
    }

    public function beforeRender(CakeEvent $event)
    {
        if ($event->subject->action == 'add') {
            $this->_setTmpAttachmentData($event);
        } elseif ($event->subject->action == 'edit' && isset($event->subject->request->params['pass'][0])) {
            $this->_setNormalAttachmentData($event);
            $this->_widgetView($event->subject->model, $event->subject->request->params['pass'][0]);
        }
    }

    protected function _setTmpAttachmentData(CakeEvent $event)
    {
        $sessionPath = "{$event->subject->model->alias}.Attachment.hash";

        $Session = $event->subject->controller->Session;

        if ($event->subject->request->is('get') || $Session->read($sessionPath) === null) {
            $Session->write($sessionPath, CakeText::uuid());
        }

        $attachmentHash = $Session->read($sessionPath);
        $event->subject->controller->set('attachmentHash', $attachmentHash);
    }

    protected function _setNormalAttachmentData(CakeEvent $event)
    {
        $event->subject->controller->set('attachmentModel', $event->subject->model->modelFullName());
        $event->subject->controller->set('attachmentForeignKey', $event->subject->request->params['pass'][0]);
    }

    protected function _widgetView($Model, $foreignKey)
    {
        if ($Model->Behaviors->enabled('Widget.Widget')) {
            $Model->widgetView($foreignKey);
            WidgetListener::deleteItemCache($Model->modelFullName(), $foreignKey);
        }
    }
}
