<?php
App::uses('AppController', 'Controller');

class AwarenessRemindersController extends AppController {

    public $helpers = [];
    public $components = [
        'Search.Prg', 'Paginator',
        'Crud.Crud' => [
            'actions' => [
                'index' => [
                    'className' => 'AdvancedFilters.AdvancedFilters',
                    'enabled' => true
                ],
            ],
            'listeners' => ['Widget.Widget']
        ],
    ];

    public function beforeFilter() {
        $this->Crud->enable(['index']);

        parent::beforeFilter();

        $this->title = __('Awareness Program Reminders');
        $this->subTitle = __('');
    }

    public function index($model = null) {
        $this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
        $this->Crud->addListener('FieldData', 'FieldData.FieldData');

        return $this->Crud->execute();
    }
}