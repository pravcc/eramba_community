<?php
App::uses('Hash', 'Utility');
App::uses('View', 'View');
App::uses('BaseAppNotification', 'AppNotification.Lib');

class NewsAppNotification extends BaseAppNotification
{
    /**
     * If redirect action is modal request.
     * 
     * @var boolean
     */
    protected $_modalRequest = true;

    /**
     * Render AppNotification icon.
     * 
     * @param View $view
     * @return string
     */
    public function renderIcon(View $view)
    {
        return $view->Html->tag('span', $view->Html->tag('i', '', ['class' => 'icon-stack-text']), [
            'class' => 'btn border-primary text-primary btn-flat btn-rounded btn-icon btn-sm app-notification-icon',
            'escape' => false
        ]);
    }

    /**
     * Link of notification redirect.
     * 
     * @return string
     */
    public function getRedirectUrl()
    {
        return Router::url(['admin' => false, 'plugin' => 'news', 'controller' => 'news', 'action' => 'view', $this->getForeignKey()]);
    }
}
