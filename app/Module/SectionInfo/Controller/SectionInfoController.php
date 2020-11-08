<?php
App::uses('SectionInfoAppController', 'SectionInfo.Controller');

class SectionInfoController extends SectionInfoAppController
{
    public $components = [];

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

        $this->title = __('Section Info');
        $this->subTitle = __('');
    }

    public function info($model)
    {
        $this->Modals->init(true);
        $this->Modals->setHeaderHeading(__('Help'));

        $itemsCount = ClassRegistry::init($model)->find('count');

        $this->set('itemsCount', $itemsCount);
        $this->set('model', $model);
    }
}
