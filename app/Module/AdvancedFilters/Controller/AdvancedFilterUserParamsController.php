<?php
App::uses('AdvancedFiltersComponent', 'Controller/Component');
App::uses('FieldDataCollection', 'FieldData.Model/FieldData');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');
App::uses('VendorAssessmentsModule', 'VendorAssessments.Lib');

class AdvancedFilterUserParamsController extends AdvancedFiltersAppController {
  
    public $components = array(
        'Ajax',
        'Crud.Crud' => [
            'actions' => [
                // 'add' => [
                //     'enabled' => true,
                //     'className' => 'AppAdd',
                //     'view' => 'add',
                //     'saveMethod' => 'saveAssociated'
                // ],
                // 'edit' => [
                //     'enabled' => true,
                //     'className' => 'AppEdit',
                //     'view' => 'add',
                //     'saveMethod' => 'saveAssociated'
                // ]
            ]
        ]
    );

    public function beforeFilter()
    {
        parent::beforeFilter();

        //allows action if session key for that authentication is set
        VendorAssessmentsModule::allowAction($this);
    }

    public function save()
    {
        $this->autoRender = false;

        $data = $this->request->data;
        
        //
        // Check if filter is saved in DB
        if (!is_numeric($data['advanced_filter_id'])) {
            return false;
        }
        //
        
        //
        // Check if advanced filter value is one of shown fields in saved filter
        $isSavingAllowed = false;
        if ($data['type'] != AdvancedFilterUserParam::TYPE_COLUMN_ORDER) {
            $AdvancedFilterValueModel = ClassRegistry::init('AdvancedFilters.AdvancedFilterValue');
            $checkFilterValue = $AdvancedFilterValueModel->find('first', [
                'fields' => [
                    'id'
                ],
                'conditions' => [
                    'advanced_filter_id' => $data['advanced_filter_id'],
                    'field' => $data['param'] . '__show',
                    'value' => 1
                ]
            ]);

            if (!empty($checkFilterValue)) {
                $isSavingAllowed = true;
            }

            //
            // Check if field is default field
            if (!$isSavingAllowed) {
                $this->loadModel('AdvancedFilter.AdvancedFilter');
                $advFilter = $this->AdvancedFilter->find('first', [
                    'fields' => [
                        'AdvancedFilter.model'
                    ],
                    'conditions' => [
                        'AdvancedFilter.id' => $data['advanced_filter_id']
                    ]
                ]);
                $modelAlias = $advFilter['AdvancedFilter']['model'];
                $Model = ClassRegistry::init($modelAlias);
                $Model->buildAdvancedFilterArgs($Model);
                foreach ($Model->advancedFilter as $group => $fieldList) {
                    foreach ($fieldList as $key => $config) {
                        if ($key === $data['param'] && $config['show_default'] == true) {
                            $isSavingAllowed = true;
                        }
                    }
                }
            }
            //
        } else {
            $isSavingAllowed = true;
        }

        if (!$isSavingAllowed) {
            return false;
        }
        //
        
        $data['user_id'] = $this->logged['id'];

        //
        // Check if record already exists, if it does, add record ID and edit existing record otherwise new record will be created
        $check = $data;
        unset($check['value']);
        unset($check['']);

        if ($check['type'] == AdvancedFilterUserParam::TYPE_COLUMN_SORT) {
            unset($check['param']);
        }

        $exist = $this->AdvancedFilterUserParam->find('first', [
            'conditions' => $check,
            'recursive' => -1
        ]);

        if (!empty($exist)) {
            $data['id'] = $exist['AdvancedFilterUserParam']['id'];
        }
        //

        $this->AdvancedFilterUserParam->create();
        $this->AdvancedFilterUserParam->set($data);
        $this->AdvancedFilterUserParam->save();
    }

}