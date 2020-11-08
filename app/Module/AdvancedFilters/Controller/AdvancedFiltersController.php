<?php
App::uses('AdvancedFiltersComponent', 'Controller/Component');
App::uses('FieldDataCollection', 'FieldData.Model/FieldData');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');

class AdvancedFiltersController extends AdvancedFiltersAppController {
    public $name = 'AdvancedFilters';
    public $uses = array('AdvancedFilters.AdvancedFilter', 'AdvancedFilterValue', 'AdvancedFilters.AdvancedFilterUserSetting');
    public $components = array('AdvancedFilters', 'Ajax', 'Crud.Crud' => [
            'actions' => [
                'add' => [
                    'enabled' => true,
                    'className' => 'AppAdd',
                    'view' => 'add',
                    'saveMethod' => 'saveAssociated'
                ],
                'edit' => [
                    'enabled' => true,
                    'className' => 'AppEdit',
                    'view' => 'add',
                    'saveMethod' => 'saveAssociated'
                ]
            ]
        ]
    );

    public function beforeFilter() {
        parent::beforeFilter();

        $this->title = __('Advanced Filters');

        $this->Crud->enable(['add', 'edit', 'delete']);

        if ($this->request->is('get') && $this->request->params['action'] === 'add') {
            $this->Auth->authorize = false;
        }
    }

    public function add($model)
    {
        $this->Modals->settings['layout'] = 'LimitlessTheme.modals/modal_advanced_filters';

        $this->Crud->on('beforeFind', [$this, '_beforeFind']);
        $this->Crud->on('beforeSave', [$this, '_beforeSettingsSave']);
        $this->Crud->on('beforeSave', [$this, '_beforeFilterSave']);
        $this->Crud->on('afterSave', [$this, '_afterFilterSave']);
        $this->Crud->on('beforeRender', [$this, '_beforeFilterRender']);
        $this->Crud->on('beforeRender', [$this, '_beforeSettingsRender']);

        return $this->Crud->execute();
    }

    public function edit($id)
    {
        $this->Modals->settings['layout'] = 'LimitlessTheme.modals/modal_advanced_filters';

        $this->Crud->on('beforeFind', [$this, '_beforeFind']);
        $this->Crud->on('beforeSave', [$this, '_beforeSettingsSave']);
        $this->Crud->on('beforeSave', [$this, '_beforeFilterSave']);
        $this->Crud->on('afterSave', [$this, '_afterFilterSave']);
        $this->Crud->on('beforeRender', [$this, '_beforeFilterRender']);
        $this->Crud->on('beforeRender', [$this, '_beforeSettingsRender']);

        return $this->Crud->execute();
    }

    public function _beforeFind(CakeEvent $event)
    {
        if (!is_array($event->subject->model->hasOne['AdvancedFilterUserSetting']['conditions'])) {
            $event->subject->model->hasOne['AdvancedFilterUserSetting']['conditions'] = [];
        }
        
        $event->subject->model->hasOne['AdvancedFilterUserSetting']['conditions'][] = 'AdvancedFilterUserSetting.user_id = ' . $this->logged['id'];
    }

    public function delete($id = null) {
        $this->subTitle = __('Delete a Filter');

        $this->Crud->on('beforeDelete', array($this, '_beforeDelete'));
        $this->Crud->on('beforeDelete', array($this, '_beforeDeleteValidate'));

        return $this->Crud->execute();
    }

    public function _beforeDelete(CakeEvent $event)
    {
        $id = $event->subject->id;
        $model = $event->subject->model;

        $isSystemFilter = $this->_isSystemFilter($model, $id);

        if ($isSystemFilter) {
            throw new ForbiddenException(__('System filter cannot be deleted.'), 1);
        }
    }

    public function _beforeDeleteValidate(CakeEvent $event)
    {
        $id = $event->subject->id;
        $model = $event->subject->model;

        $inUse = false;

        if (AppModule::loaded('NotificationSystem')) {
            // check if notifications use this filter
            $inUse = (bool) ClassRegistry::init('NotificationSystem.NotificationSystem')->find('count', [
                'conditions' => [
                    'NotificationSystem.advanced_filter_id' => $event->subject->id
                ],
                'recursive' => -1
            ]);
        }

        // or is in use in reports
        if (!$inUse && AppModule::loaded('Reports')) {
            $inUse = (bool) ClassRegistry::init('Reports.ReportBlockFilterSetting')->find('count', [
                'conditions' => [
                    'ReportBlockFilterSetting.advanced_filter_id' => $event->subject->id
                ],
                'recursive' => -1
            ]);
        }

        if ($inUse) {
            $event->stopPropagation();
            $event->subject->crud->action()->config('messages.error.text', __('This filter is already used in Notifications or Reports. Please review them first before deleting.'));
        }
    }

    protected function _isSystemFilter($model, $id)
    {
        if (empty($model) || empty($id)) {
            return false;
        }

        return (boolean) $model->find('count', [
            'conditions' => [
                'AdvancedFilter.id' => $id,
                'AdvancedFilter.system_filter' => 1
            ],
            'recursive' => -1
        ]);
    }

    protected function _listArgs(CakeEvent $e)
    {
        $action = $e->subject->action;

        if ($action == 'add') {
            $args = $e->subject->request->params['pass'];
        }

        if ($action == 'edit') {
            $args = $e->subject->request->params['pass'];
            $model = $e->subject->model;
            $id = $args[0];

            $item = $model->find('first', [
                'conditions' => [
                    $model->alias . '.' . $model->primaryKey => $id
                ],
                'fields' => [
                    $model->alias . '.model',
                ],
                'recursive' => -1
            ]);

            $args = [
                $item[$model->alias]['model']
            ];
        }

        return $args;
    }

    protected function _getId(CakeEvent $e)
    {
        $action = $e->subject->action;
        if ($action == 'edit') {
            return $e->subject->request->data['AdvancedFilter']['id'];
        }

        return null;
    }

    public function _beforeSettingsSave(CakeEvent $e)
    {
        $request = $e->subject->request;
        $args = $this->_listArgs($e);
        $model = $args[0];

        $e->subject->request->data['AdvancedFilter']['model'] = $model;
        $e->subject->request->data['AdvancedFilter']['user_id'] = $this->logged['id'];
        $e->subject->request->data['AdvancedFilterUserSetting']['user_id'] = $this->logged['id'];
        $e->subject->request->data['AdvancedFilterUserSetting']['model'] = $model;

        // for edit action we have to configure 'user setting' id so it wont duplicate
        if (($id = $this->_getId($e)) !== null) {
            $userSettingId = $e->subject->model->AdvancedFilterUserSetting->syncSetting($id, $this->logged['id']);

            $e->subject->request->data['AdvancedFilterUserSetting']['id'] = $userSettingId;
        }
    }

    public function _beforeFilterSave(CakeEvent $e)
    {
        $request = $e->subject->request;
        $args = $this->_listArgs($e);
        $model = $args[0];

        if (($id = $this->_getId($e)) !== null) {
            $AdvancedFilterValue = $this->AdvancedFilter->AdvancedFilterValue;
            $AdvancedFilterValue->deleteAll(['advanced_filter_id' => $id]);
        }

        $data = [];
        // debug($request->data);
        $processParams = array_merge($request->data[$model], $request->data['AdvancedFilterValue']);
        foreach ($processParams as $field => $value) {
            if ($field == 'advanced_filter_id') {
                continue;
            }

            $data[] = [
                'field' => $field,
                'value' => (is_array($value)) ? implode(',', $value) : $value,
                'many' => (is_array($value)) ? ADVANCED_FILTER_VALUE_MANY : ADVANCED_FILTER_VALUE_ONE
            ];
        }

        $request->data['AdvancedFilter']['model'] = $model;
        $request->data['AdvancedFilterValue'] = $data;

        unset($request->data[$model]);

        //
        // Set System Filter (disabled) values to values from DB
        if ($this->_isSystemFilter($e->subject->model, $this->_getId($e))) {
            $dbAdvancedFilterData = $this->AdvancedFilter->find('first', [
                'fields' => [
                    'AdvancedFilter.name', 'AdvancedFilter.description', 'AdvancedFilter.private'
                ],
                'conditions' => [
                    'AdvancedFilter.id' => $this->_getId($e)
                ],
                'recursive' => -1
            ]);

            if (!empty($dbAdvancedFilterData)) {
                $request->data['AdvancedFilter']['name'] = $dbAdvancedFilterData['AdvancedFilter']['name'];
                $request->data['AdvancedFilter']['description'] = $dbAdvancedFilterData['AdvancedFilter']['description'];
                $request->data['AdvancedFilter']['private'] = $dbAdvancedFilterData['AdvancedFilter']['private'];
            }
        }
        //
    }

    public function _afterFilterSave(CakeEvent $e)
    {
        $id = $this->_getId($e);
        AdvancedFiltersObject::setJustSavedFilter($id, $this->request->query);
    }

    public function _beforeFilterRender(CakeEvent $e)
    {
        $request = $e->subject->request;

        $args = $this->_listArgs($e);
        $model = $args[0];
        $Model = ClassRegistry::init($model);

        if ($Model->Behaviors->enabled('AdvancedFilters.AdvancedFilters')) {
            $Model->buildAdvancedFilterArgs();
        }

        $this->set('filterModel', $model);

        $this->loadModel($model);
        $this->AdvancedFilters->setFilterSettings($model, true);
        $this->AdvancedFilters->setFilterData($model);

        $FieldDataCondsCollection = $this->AdvancedFilter->AdvancedFilterValue->getFieldCollection();
        $this->set($FieldDataCondsCollection->getViewOptions('AdvancedFilterValueCollection', $model));

        $filterValues = [];
        // check if this is add or edit action
        if (($id = $this->_getId($e)) !== null) {
            $filterValues = $this->AdvancedFilter->getFormattedValues($id);
            $this->set('activeFilterId', $id);
        }
        
        $specialKeys = [
            '_limit',
            '_order_column',
            '_order_direction'
        ];
        $query = $request->query;

        foreach ($specialKeys as $specialKey) {
            if (isset($filterValues[$specialKey])) {
                $request->data['AdvancedFilterValue'][$specialKey] = $filterValues[$specialKey];
                unset($filterValues[$specialKey]);
            }

            if (isset($query[$specialKey])) {
                $request->data['AdvancedFilterValue'][$specialKey] = $query[$specialKey];
                unset($query[$specialKey]);
            }
        }

        $submittedQuery = AdvancedFiltersObject::trimFilterQuery($query);
        unset($submittedQuery['_page']);
        unset($submittedQuery['_pageLimit']);
        unset($submittedQuery['_limit']);
        unset($submittedQuery['_order_column']);
        unset($submittedQuery['_order_direction']);
        
        if (empty($submittedQuery)) {
            $request->data[$model] = $filterValues;
        } else {
            $request->data[$model] = $submittedQuery;
        }

        foreach ($Model->advancedFilter as $fieldSet) {
            foreach ($fieldSet as $field => $fieldData) {
                if (isset($request->data[$model][$field]) && is_string($request->data[$model][$field]) && isset($fieldData['type']) && $fieldData['type'] == 'multiple_select') {
                    $request->data[$model][$field] = explode(',', $request->data[$model][$field]);
                }

                $showField = $field . '__show';

                $showDefault = !empty($fieldData['show_default']);
                $showDefault &= ((!isset($request->data[$model]['advanced_filter'])) || !isset($request->data[$model][$showField]));
                if ($showDefault) {
                    $request->data[$model][$showField] = 1;
                }
            }
        }
    }

    public function _beforeSettingsRender(CakeEvent $e)
    {
        $subject = $e->subject;
        $model = $subject->model;
        $controller = $subject->controller;

        $AdvancedFilterUserSettingCollection = $model->AdvancedFilterUserSetting->getFieldCollection();
        $this->set($AdvancedFilterUserSettingCollection->getViewOptions('AdvancedFilterUserSettingCollection'));

        // set into the controller if current filter being edited is a system filter
        // which is not possible to change much
        // @see AdvancedFiltersHelper
        $id = $this->_getId($e);
        $isSystemFilter = $this->_isSystemFilter($model, $id);

        $controller->set(compact('isSystemFilter'));
    }

    /**
     * redirect to AdvancedFilter detail
     * 
     * @param int $id
     */
    public function redirectAdvancedFilter($id) {
        $id = (int) $id;

        $filter = $this->AdvancedFilter->get($id);
        if (empty($filter)) {
            throw new NotFoundException();
        }

        $model = $filter['AdvancedFilter']['model'];
        $this->loadModel($model);

        $url = $this->{$model}->getMappedRoute([
            '?' => [
                'advanced_filter' => true,
                'advanced_filter_id' => $id
            ]
        ]);

        return $this->redirect($url);
    }

    public function exportCsvAll($id) {
        $this->autoRender = false;

        $AdvancedFiltersObject = new AdvancedFiltersObject($id);
        $AdvancedFiltersObject->filter('all', [
            'applyLimit' => true
        ]);

        $this->response->body($AdvancedFiltersObject->csv());
        $this->response->type('csv');
        return $this->response->download($AdvancedFiltersObject->getName() . '_export.csv');
    }

    public function exportCsvAllQuery($model) {
        $this->autoRender = false;

        $AdvancedFiltersObject = new AdvancedFiltersObject();

        $AdvancedFiltersObject->setModel(ClassRegistry::init($model));
        $AdvancedFiltersObject->setFilterValues($this->request->query);
        $AdvancedFiltersObject->filter('all', [
            'applyLimit' => true
        ]);

        $this->response->body($AdvancedFiltersObject->csv());
        $this->response->type('csv');
        return $this->response->download($AdvancedFiltersObject->getName() . '_export.csv');
    }

    /**
     * @deprecated but still in use
     */
    public function exportDailyCountResults($filterId) {
        $this->AdvancedFiltersCron = $this->Components->load('AdvancedFiltersCron');
        $this->AdvancedFiltersCron->initialize($this);

        $fileName = $this->AdvancedFiltersCron->exportDailyCountResults($filterId);
        if (empty($fileName)) {
            throw new NotFoundException();
        }

        $this->set('_delimiter', Configure::read('Eramba.Settings.CSV_DELIMITER'));
        $this->response->download($fileName . '.csv');
        $this->viewClass = 'CsvView.Csv';
    }

    /**
     * @deprecated but still in use
     */
    public function exportDailyDataResults($filterId) {
        $this->AdvancedFiltersCron = $this->Components->load('AdvancedFiltersCron');
        $this->AdvancedFiltersCron->initialize($this);

        $fileName = $this->AdvancedFiltersCron->exportDailyDataResults($filterId);
        if (empty($fileName)) {
            throw new NotFoundException();
        }
        
        $this->set('_delimiter', Configure::read('Eramba.Settings.CSV_DELIMITER'));
        $this->response->download($fileName . '.csv');
        $this->viewClass = 'CsvView.Csv';
    }
}
