<?php
App::uses('AppNotificationAppController', 'AppNotification.Controller');

class AppNotificationsController extends AppNotificationAppController {

    public $components = [
        'Paginator'
    ];
    public $helpers = [];

    protected $_appControllerConfig = [
        'components' => [
        ],
        'helpers' => [
        ],
        'elements' => [
        ]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->Auth->authorize = false;

        $this->title = __('App Notifications');
        $this->subTitle = __('');
    }

    public function list($viewTimestamp)
    {
        $viewDatetime = date('Y-m-d H:i:s', $viewTimestamp);

        $this->Paginator->settings['limit'] = 10;
        $this->Paginator->settings['order'] = ['AppNotification.created' => 'DESC'];
        $this->Paginator->settings['conditions'] = $this->AppNotifications->getConditions();

        $data = $this->Paginator->paginate('AppNotification.AppNotification');

        foreach ($data as $key => $item) {
            list($plugin, $className) = pluginSplit($item['AppNotification']['notification'], true);

            App::uses($className, $plugin . 'Lib/AppNotification');

            $notification = new $className(ClassRegistry::init('AppNotification.AppNotification')->getItemDataEntity($item));

            if ($viewDatetime > $notification->getCreated()) {
                $notification->setSeen(true);
            }

            $data[$key] = $notification;
        }

        $this->AppNotifications->notificationsView();

        $this->set('viewTimestamp', $viewTimestamp);
        $this->set('appNotifications', $data);
    }
}
