<?php
App::uses('AppController', 'Controller');
App::uses('DataAssetSetting', 'Model');

class DataAssetSettingsController extends AppController
{
    public $uses = ['DataAssetSetting', 'DataAssetInstance'];
    public $helpers = ['UserFields.UserField'];
    public $components = [
        'Search.Prg', 'Paginator', 'ObjectStatus.ObjectStatus',
        'Ajax' => [
            'actions' => ['setup'],
            'formDataActions' => ['setup']
        ],
        'Crud.Crud' => [
            'actions' => [
                'add' => [
                    'saveOptions' => [
                        'deep' => false
                    ]
                ],
                'edit' => [
                    'saveOptions' => [
                        'deep' => false
                    ]
                ]
            ]
        ],
        'UserFields.UserFields' => [
            'fields' => ['DataOwner']
        ],
    ];

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
        $this->Crud->enable(['add', 'edit', 'history', 'restore']);

        parent::beforeFilter();

        $this->title = __('Data Asset Settings');
        $this->subTitle = __('');
    }

    public function add($dataAssetInstanceId)
    {
        $dataAssetInstance = $this->DataAssetInstance->getItem($dataAssetInstanceId);

        $setRequestDate = function(CakeEvent $event) use ($dataAssetInstance)  {
            $event->subject->request->data['DataAssetSetting']['data_asset_instance_id'] = $dataAssetInstance['DataAssetInstance']['id'];
            $event->subject->request->data['DataAssetSetting']['name'] = $dataAssetInstance['Asset']['name'];
        };

        $this->Crud->on('beforeRender', $setRequestDate);
        $this->Crud->on('beforeSave', $setRequestDate);
        $this->Crud->on('afterFind', array($this, '_afterFind'));

        $this->_adaptFormData();

        return $this->Crud->execute();
    }

    public function edit($id)
    {
        $this->Crud->on('beforeRender', function() {
            $this->_adaptFormData();
        });

        $this->Crud->on('afterFind', array($this, '_afterFind'));

        return $this->Crud->execute();
    }

    protected function _adaptFormData()
    {
        if (empty($this->request->data['DataAssetSetting']['gdpr_enabled'])) {
            $this->_FieldDataCollection = new FieldDataCollection([], $this->DataAssetSetting);

            $this->_FieldDataCollection->add($this->DataAssetSetting->getFieldDataEntity('name'));
            $this->_FieldDataCollection->add($this->DataAssetSetting->getFieldDataEntity('DataOwner'));
            $this->_FieldDataCollection->add($this->DataAssetSetting->getFieldDataEntity('gdpr_enabled'));
        }
    }

    public function _afterFind(CakeEvent $event) {
        $data = $event->subject->item;
        if (!empty($data['SupervisoryAuthority'])) {
            $data['DataAssetSetting']['SupervisoryAuthority'] = Hash::extract($data['SupervisoryAuthority'], '{n}.country_id');
        }
        $event->subject->item = $data;
    }

    public function history($id)
    {
        return $this->Crud->execute();
    }

    public function restore($autidId)
    {
        return $this->Crud->execute();
    }
}
