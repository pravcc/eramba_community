<?php
App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('Hash', 'Utility');
App::uses('AuthComponent', 'Controller/Component');

class AppNotificationsComponent extends Component 
{
    public $components = [];
    public $settings = [];

    public function __construct(ComponentCollection $collection, $settings = [])
    {
        if (empty($this->settings)) {
            $this->settings = [];
        }

        $settings = array_merge($this->settings, (array) $settings);

        parent::__construct($collection, $settings);
    }

    public function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }

    public function beforeRender(Controller $controller)
    {
        if (!empty($this->controller->Auth->user()) 
            && !$this->controller->request->is('ajax') 
            && !in_array($this->controller->params['action'], ['login', 'logout', 'prepareAccount'])
        ) {
            $this->setAppNotificationData();
        }
    }

    /**
     * Set app notifications data to View.
     *
     * @return void
     */
    public function setAppNotificationData()
    {
        $AppNotification = ClassRegistry::init('AppNotification.AppNotification');
        $AppNotification->Behaviors->load('Visualisation.Visualisation');

        $cacheKey = 'header_data_user_' . AuthComponent::user('id');

        if (($data = Cache::read($cacheKey, 'app_notification')) === false) {
            $view = ClassRegistry::init('AppNotification.AppNotificationView')->find('first', [
                'conditions' => [
                    'AppNotificationView.user_id' => AuthComponent::user('id')
                ],
                'recursive' => -1
            ]);

            $lastViewDatetime = (!empty($view)) ? $view['AppNotificationView']['notifications_view'] : AuthComponent::user('created');

            $alertsCount = $AppNotification->find('count', [
                'conditions' => array_merge($this->getConditions(), [
                    'AppNotification.created >' => $lastViewDatetime,
                ]),
                'recursive' => -1
            ]);

            $data = [
                'alertsCount' => $alertsCount,
                'lastViewDatetime' => $lastViewDatetime
            ];

            Cache::write($cacheKey, $data, 'app_notification');
        }

        $this->controller->set('appNotification', $data);
    }

    /**
     * Get find conditions for users AppNotifications.
     * 
     * @return array
     */
    public function getConditions()
    {
        return [
            'AppNotification.title IS NOT NULL',
            'AppNotification.title !=' => '',
            $this->_getAclRules()
        ];
    }

    protected function _getAclRules()
    {
        $model = 'AppNotification.AppNotification';

        // fresh instance of a controller temporarily to check ACL rules and not affect current flow
        $controller = new Controller(new CakeRequest());
        $collection = new ComponentCollection();
        $controller->logged = $this->controller->logged;

        $controller->modelClass = $model;
        $controller->Crud = $controller->Components->load('Crud.Crud');
        $controller->Components->Crud->initialize($controller);
        $controller->Crud->useModel($controller->modelClass);
        $controller->Visualisation = $controller->Components->load('Visualisation.Visualisation');
        $controller->Components->Visualisation->initialize($controller);
        $Model = ClassRegistry::init($controller->modelClass);
        $controller->Crud->addListener('Visualisation', 'Visualisation.Visualisation');
        $Listener = $controller->Crud->listener('Visualisation');
// ddd($Model);
        // if ($controller->Visualisation->isEnabled()) {
            $conds = $Listener->getConditions($Model);
            // ddd($conds);
            return $conds;
        // }
    }

    /**
     * Records app notifications view by user.
     *
     * @return boolean
     */
    public function notificationsView()
    {
        return ClassRegistry::init('AppNotification.AppNotificationView')->createOrUpdateView(AuthComponent::user('id'), date('Y-m-d H:i:s'));
    }
}
