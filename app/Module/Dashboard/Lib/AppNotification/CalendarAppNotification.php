<?php
App::uses('Hash', 'Utility');
App::uses('View', 'View');
App::uses('BaseAppNotification', 'AppNotification.Lib');

class CalendarAppNotification extends BaseAppNotification
{
    /**
     * Render AppNotification icon.
     * 
     * @param View $view
     * @return string
     */
    public function renderIcon(View $view)
    {
        return $view->Html->tag('span', $view->Html->tag('i', '', ['class' => 'icon-calendar52']), [
            'class' => 'btn border-success text-success btn-flat btn-rounded btn-icon btn-sm app-notification-icon',
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
        return Router::url(['admin' => false, 'plugin' => 'dashboard', 'controller' => 'dashboardCalendarEvents', 'action' => 'index']);
    }
}
