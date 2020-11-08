<?php
App::uses('AppController', 'Controller');

class AwarenessTrainingsController extends AppController {

    public $helpers = [];
    public $components = [
        'Search.Prg', 'Paginator', 'AdvancedFilters',
        'Crud.Crud' => [
            'actions' => [
                'index' => [
                    'className' => 'AdvancedFilters.AdvancedFilters',
                    'enabled' => true
                ],
            ],
        ],
    ];

    public function beforeFilter() {
        $this->Crud->enable(['index']);

        parent::beforeFilter();

        $this->title = __('Awareness Trainings');
        $this->subTitle = __('');
    }

    public function index($model = null) {
        $this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
        $this->Crud->addListener('FieldData', 'FieldData.FieldData');
        
        return $this->Crud->execute();
    }
}