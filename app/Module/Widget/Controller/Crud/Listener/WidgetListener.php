<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');
App::uses('AuthComponent', 'Controller/Component');
App::uses('WidgetCrudView', 'Widget.Controller/Crud/View');
App::uses('CakeEvent', 'Event');
App::uses('CakeText', 'Utility');

/**
 * Widget Listener
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 */
class WidgetListener extends CrudListener
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
            'Crud.beforeRender' => ['callable' => 'beforeRender', 'priority' => 50],
            'Crud.beforeHandle' => ['callable' => 'beforeHandle', 'priority' => 50],
            'Crud.beforeFilter' => ['callable' => 'beforeFilter', 'priority' => 50],
            'Crud.afterSave' => ['callable' => 'afterSave', 'priority' => 50],
            'AdvancedFilter.afterFind' => ['callable' => 'afterFilterFind', 'priority' => 55],
        ];
    }

    public function beforeHandle(CakeEvent $event)
    {
        $Model = $this->_model();

        $this->_ensureBehavior($Model);

        // unset widget request data
        if ($this->_isFormWidgetRequest($event)) {
            $this->_unsetWidgetRequestData($event);
        } 
    }

    /**
     * Load WidgetBehavior if is not loaded.
     * 
     * @param Model $Model
     * @return void
     */
    protected function _ensureBehavior(Model $Model)
    {
        if ($Model->Behaviors->loaded('Widget.Widget')) {
            return;
        }

        $Model->Behaviors->load('Widget.Widget');
        $Model->Behaviors->Widget->setup($Model);
    }

    /**
     * Check if we are in add/edit with widget.
     * 
     * @param CakeEvent $event
     * @return boolean
     */
    protected function _isFormWidgetRequest(CakeEvent $event)
    {
        $request = $this->_request();
        $action = $event->subject->action;

        return ($action == 'add' || ($action == 'edit' && !empty($request->params['pass'][0])));
    }

    /**
     * Check if we are in widget modal request.
     * 
     * @param CakeEvent $event
     * @return boolean
     */
    protected function _isModalWidgetRequest(CakeEvent $event)
    {
        $request = $this->_request();
        $action = $event->subject->action;

        return $request->params['controller'] === 'widget' && $action == 'index';
    }

    /**
     * Unset not wanted widget request data.
     * 
     * @param CakeEvent $event
     * @return void
     */
    protected function _unsetWidgetRequestData(CakeEvent $event)
    {
        $request = $this->_request();

        unset($request->data['Comment']['message']);
    }

    /**
     * Before render callback.
     * 
     * @param CakeEvent $event
     * @return void
     */
    public function beforeRender(CakeEvent $event)
    {
        // create WidgetCrudView objct
        $WidgetCrudView = new WidgetCrudView($event->subject);

        $request = $this->_request();
        $action = $event->subject->action;

        if ($this->_isFormWidgetRequest($event)) {
            // set widget field data
            $this->_setFieldData($event);

            // if we are in add action we have to display tmp widget
            if ($action == 'add') {
                if ($this->_request()->is('get')) {
                    $this->_setTmpHash();
                }

                $WidgetCrudView->subjectHash($this->_getTmpHeash());
            }
            else {// othervise set normal widget
                $WidgetCrudView->subjectModel($this->_model()->modelFullName());
                $WidgetCrudView->subjectForeignKey($request->params['pass'][0]);

                $this->_widgetView($this->_model(), $request->params['pass'][0]);
            }
        }
        elseif ($this->_isModalWidgetRequest($event)) {
            $WidgetCrudView->subjectModel($request->params['pass'][0]);
            $WidgetCrudView->subjectForeignKey($request->params['pass'][1]);

            $WidgetCrudView->isModalRequest(true);

            $this->_widgetView(ClassRegistry::init($request->params['pass'][0]), $request->params['pass'][1]);
        }

        // set WidgetCrudView object
        $this->_controller()->set('Widget', $WidgetCrudView);
    }

    /**
     * Get tmp hash from session.
     * 
     * @return string 
     */
    protected function _getTmpHeash()
    {
        $Session = $this->_controller()->Session;

        $hash = $Session->read($this->_getHashSessionPath());

        if (empty($hash)) {
            $hash = $this->_setTmpHash();
        }

        return $hash;
    }

    /**
     * Create new tmp hash and write it to session, return created hash.
     * 
     * @return string 
     */
    protected function _setTmpHash()
    {
        $Session = $this->_controller()->Session;

        $hash = CakeText::uuid();

        $Session->write($this->_getHashSessionPath(), CakeText::uuid());

        return $hash;
    }

    /**
     * Return widget session path to hash. Path is based on subject model.
     * 
     * @return string
     */
    protected function _getHashSessionPath()
    {
        $modelName = $this->_model()->name;

        return "{$modelName}.Widget.hash";
    }

    

    /**
     * Set widget-add field to _FieldDataCollection to display widget tab (comments and attachments) in form.
     * 
     * @param CakeEvent $event
     * @return void
     */
    protected function _setFieldData(CakeEvent $event)
    {
        $field = ClassRegistry::init('Widget.Widget')->getFieldDataEntity('widget-add');

        $event->subject->controller->_FieldDataCollection->add($field);

        // ddd($event->subject->controller->_FieldDataCollection->get('1-value'));

        $event->subject->controller->set('CommentFieldDataCollection', ClassRegistry::init('Comments.Comment')->getFieldCollection());
    }

    /**
     * After save callback.
     * 
     * @param CakeEvent $event
     * @return void
     */
    public function afterSave(CakeEvent $event)
    {
        $hash = $this->_getTmpHeash();

        if ($this->_isFormWidgetRequest($event) && $event->subject->success && $event->subject->created && !empty($hash)) {
            // bind tmp comments and attachments to created object
            ClassRegistry::init('Attachments.Attachment')->tmpToNormal($hash, $event->subject->model->modelFullName(), $event->subject->id);
            ClassRegistry::init('Comments.Comment')->tmpToNormal($hash, $event->subject->model->modelFullName(), $event->subject->id);

            // trigger widget view
            $this->_widgetView($event->subject->model, $event->subject->id);
        }
    }

    /**
     * Trigger widget view, remove widget info cache.
     * 
     * @param Model $Model
     * @param int $foreignKey
     * @return void
     */
    protected function _widgetView($Model, $foreignKey)
    {
        if ($Model->Behaviors->enabled('Widget.Widget')) {
            $Model->widgetView($foreignKey);
            self::deleteItemCache($Model->modelFullName(), $foreignKey);
        }
    }

    /**
     * Before filter callback.
     * 
     * @param CakeEvent $event
     * @return void
     */
    public function beforeFilter(CakeEvent $event)
    {
        // and attach an event to the advanced filter object to additionally make changes to the final query
        $AdvancedFiltersObject = $event->subject->AdvancedFiltersObject;
        $this->attachListener($AdvancedFiltersObject);
    }

    public function attachListener(AdvancedFiltersObject $Filter)
    {
        $Filter->getEventManager()->attach($this);
    }
    
    public function afterFilterFind(CakeEvent $event)
    {
        $this->setWidgetData($event->result);
    }

    /**
     * Set widget data to ItemDataProperty.
     *
     * @param ItemDataCollection $data
     * @param void
     */
    public static function setWidgetData($data)
    {
        if (!($data instanceof ItemDataCollection) || $data->count() <= 0) {
            return;
        }

        $widgetData = self::_getWidgetData($data);

        foreach ($data as $Item) {
            if ($Item->Properties->enabled('Widget.Widget') && isset($widgetData[$Item->getPrimary()])) {
                $Item->setWidgetData($widgetData[$Item->getPrimary()]);
            }
        }
    }

    /**
     * Calculate and return widget data.
     * 
     * @param ItemDataCollection $ItemDataCollection
     * @return array Widget data.
     */
    protected static function _getWidgetData($ItemDataCollection)
    {
        $loggedUser = AuthComponent::user('id');

        $finalData = [];
        $newData = [];

        $noDataIds = [];

        $users = self::_getUsers();
        $defaultViewTime = self::_getDefaultViewTime();

        $modelName = $ItemDataCollection->getModel()->modelFullName();

        // check items if they are in the cache or not
        foreach ($ItemDataCollection as $Item) {
            $itemCacheKey =  self::getItemCacheKey($modelName, $Item->getPrimary());

            $itemData = Cache::read($itemCacheKey, 'widget_data');

            if ($itemData === false || !in_array($loggedUser, $itemData['users'])) {
                $noDataIds[$Item->getPrimary()] = $Item->getPrimary();
                $newData[$Item->getPrimary()] = [
                    'users' => $users,
                    'comments' => [
                        'count_total' => 0,
                        'user_data' => []
                    ],
                    'attachments' => [
                        'count_total' => 0,
                        'user_data' => []
                    ]
                ];

                foreach ($users as $userId) {
                    $newData[$Item->getPrimary()]['comments']['user_data'][$userId] = [
                        'view' => $defaultViewTime,
                        'count_unseen' => 0
                    ];

                    $newData[$Item->getPrimary()]['attachments']['user_data'][$userId] = [
                        'view' => $defaultViewTime,
                        'count_unseen' => 0
                    ];
                }
            }
            else {
                $finalData[$Item->getPrimary()] = $itemData;
            }
        }

        // if there is no need to get fresh data return cache
        if (empty($noDataIds)) {
            return $finalData;
        }

        // get and set widget logs data
        $widgetView = ClassRegistry::init('Widget.WidgetView')->find('all', [
            'conditions' => [
                'WidgetView.model' => $modelName,
                'WidgetView.foreign_key' => $noDataIds,
            ],
            'fields' => [
                'WidgetView.foreign_key', 'WidgetView.user_id', 'WidgetView.comments_view', 'WidgetView.attachments_view'
            ],
            'recursive' => -1,
        ]);

        foreach ($widgetView as $view) {
            $foreignKey = $view['WidgetView']['foreign_key'];
            $userId = $view['WidgetView']['user_id'];

            if (!empty($view['WidgetView']['comments_view'])) {
                $newData[$foreignKey]['comments']['user_data'][$userId] = [
                    'view' => $view['WidgetView']['comments_view'],
                    'count_unseen' => 0
                ];
            }

            if (!empty($view['WidgetView']['attachments_view'])) {
                $newData[$foreignKey]['attachments']['user_data'][$userId] = [
                    'view' => $view['WidgetView']['attachments_view'],
                    'count_unseen' => 0
                ];
            }
        }

        // set comments data
        $commentsData = ClassRegistry::init('Comments.Comment')->find('all', [
            'conditions' => [
                'Comment.model' => $modelName,
                'Comment.foreign_key' => $noDataIds,
            ],
            'fields' => [
                'Comment.foreign_key', 'Comment.created',
            ],
            'recursive' => -1
        ]);

        foreach ($commentsData as $comment) {
            $foreignKey = $comment['Comment']['foreign_key'];

            $newData[$foreignKey]['comments']['count_total']++;

            foreach ($newData[$foreignKey]['comments']['user_data'] as $userId => $userData) {
                if ($comment['Comment']['created'] > $userData['view']) {
                    $newData[$foreignKey]['comments']['user_data'][$userId]['count_unseen']++;
                }
            }
        }

        // set attachments data
        $attachmentsData = ClassRegistry::init('Attachments.Attachment')->find('all', [
            'conditions' => [
                'Attachment.model' => $modelName,
                'Attachment.foreign_key' => $noDataIds,
            ],
            'fields' => [
                'Attachment.foreign_key', 'Attachment.created',
            ],
            'recursive' => -1
        ]);

        foreach ($attachmentsData as $attachment) {
            $foreignKey = $attachment['Attachment']['foreign_key'];

            $newData[$foreignKey]['attachments']['count_total']++;

            foreach ($newData[$foreignKey]['attachments']['user_data'] as $userId => $userData) {
                if ($attachment['Attachment']['created'] > $userData['view']) {
                    $newData[$foreignKey]['attachments']['user_data'][$userId]['count_unseen']++;
                }
            }
        }

        // write new data to cache
        foreach ($newData as $itemId => $itemData) {
            $itemCacheKey = self::getItemCacheKey($modelName, $itemId);

            Cache::write($itemCacheKey, $itemData, 'widget_data');
        }

        // merge with cached data with new data
        foreach ($newData as $key => $item) {
            $finalData[$key] = $item;
        }

        return $finalData;
    }

    /**
     * Get actual user list.
     * 
     * @return array List of user ids.
     */
    protected function _getUsers()
    {
        if (($users = Cache::read('user_list', 'widget_settings')) === false) {
            $users = ClassRegistry::init('User')->find('list', [
                'fields' => ['User.id'],
                'recursive' => -1
            ]);

            Cache::write('user_list', $users, 'widget_settings');
        }

        return $users;
    }

    /**
     * Get default view time.
     * 
     * @return string Sql datetime format.
     */
    protected function _getDefaultViewTime()
    {
        if (($defaultViewTime = Cache::read('default_view_time', 'widget_settings')) === false) {
            $Phinxlog = ClassRegistry::init('Phinxlog');
            $Phinxlog->useTable = 'phinxlog';

            $log = $Phinxlog->find('first', [
                'conditions' => [
                    'Phinxlog.migration_name' => 'WidgetViewsMigration',
                ],
                'fields' => ['Phinxlog.end_time'],
                'recursive' => -1
            ]);

            $defaultViewTime = (!empty($log['Phinxlog']['end_time'])) ? $log['Phinxlog']['end_time'] : '2019-01-23 00:00:00';

            Cache::write('default_view_time', $defaultViewTime, 'widget_settings');
        }

        return $defaultViewTime;
    }

    /**
     * Returns widget item cache key.
     * 
     * @param string $modelName Model name.
     * @param int $id Item id.
     * @return string
     */
    public static function getItemCacheKey($modelName, $id)
    {
        return "item_{$modelName}_{$id}";
    }

    /**
     * Delete widget item cache.
     * 
     * @param string $modelName Model name.
     * @param int $id Item id.
     * @return boolean
     */
    public static function deleteItemCache($modelName, $id)
    {
        return Cache::delete(self::getItemCacheKey($modelName, $id), 'widget_data');
    }
}
