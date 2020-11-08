<?php
App::uses('AppHelper', 'View/Helper');
App::uses('CakeEventListener', 'Event');

class CrudHelper extends AppHelper implements CakeEventListener
{
    public function __construct(View $view, $settings = [])
    {
        parent::__construct($view, $settings);

        $view->getEventManager()->attach($this);
    }

    public function implementedEvents()
    {
        return [
            // 'LayoutToolbar.beforeRender' => ['callable' => 'beforeLayoutToolbarRender', 'priority' => 50],
            // 'PageToolbar.beforeRender' => ['callable' => 'beforePageToolbarRender', 'priority' => 50],
            // 'ItemDropdown.beforeRender' => ['callable' => 'beforeItemDropdownRender', 'priority' => 50],
        ];
    }
}