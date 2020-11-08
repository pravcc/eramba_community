<?php
App::uses('AppController', 'Controller');

class AwarenessProgramUsersController extends AppController {

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
            'listeners' => ['Api', 'ApiPagination', '.SubSection', 'Widget.Widget']
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
        parent::beforeFilter();

        $this->title = __('Awareness Program Users');
        $this->subTitle = false;
    }

    public function index($model = null)
    {
        $this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
        $this->Crud->addListener('FieldData', 'FieldData.FieldData');

        return $this->Crud->execute();
    }
}