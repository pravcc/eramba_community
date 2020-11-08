<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('SectionInfoView', 'SectionInfo.Controller/Crud/View');

/**
 * Widget Listener
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 */
class SectionInfoListener extends CrudListener
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
            'Crud.beforeRender' => array('callable' => 'beforeRender', 'priority' => 50),
        ];
    }

    public function beforeRender(CakeEvent $event)
    {
        $this->_controller()->set('SectionInfo', new SectionInfoView($event->subject));
    }
}
