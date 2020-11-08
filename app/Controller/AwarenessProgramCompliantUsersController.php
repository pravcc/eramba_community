<?php
App::uses('AwarenessProgramUsersController', 'Controller');

class AwarenessProgramCompliantUsersController extends AwarenessProgramUsersController {

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

        $this->title = __('Awareness Program Compliant Users');
        $this->subTitle = false;
    }
}