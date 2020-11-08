<?php
App::uses('ErambaCakeEmail', 'Network/Email');
App::uses('AppController', 'Controller');

class QueueController extends AppController
{
    public $helpers = [
        'Html',
        'Form',
        'Paginator'
    ];

    public $components = [
        'Session', 'Paginator', 'Search.Prg', 'AdvancedFilters',
        'Crud.Crud' => [
            'actions' => [
                'index' => [
                    'className' => 'AdvancedFilters.AdvancedFilters',
                    'enabled' => true
                ]
            ],
            'listeners' => [
                'Widget.Widget', 'BulkActions.BulkActions'
            ]
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
        $this->Crud->enable(['index', 'delete']);

        parent::beforeFilter();

        $this->title = __('Queue');
        $this->subTitle = __('This queue holds emails until they are sent (every hour at a rate defined under System / Settings / Email Settings)');

        // $this->Crud->addListener('NotificationSystem', 'NotificationSystem.NotificationSystem');
    }

    public function index()
    {
        $this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
        // $this->Crud->addListener('Trash', 'Trash.Trash');
        // $this->Crud->addListener('Visualisation', 'Visualisation.Visualisation');
        $this->Crud->addListener('FieldData', 'FieldData.FieldData');
        $this->Crud->addListener('ImportTool', 'ImportTool.ImportTool');
        $this->Crud->addListener('SettingsSubSection', 'SettingsSubSection');

        $this->Paginator->settings['order'] = array('Queue.created' => 'DESC');

        return $this->Crud->execute();
    }

    public function delete($id = null) {
        $this->subTitle = __('Delete Queued Email');

        return $this->Crud->execute();
    }

    // public function flush() {
    //     $ret = ErambaCakeEmail::sendQueue();

    //     if ($ret) {
    //         $this->Flash->success(__('Queue has been flushed successfully.'));
    //     }
    //     else {
    //         $this->Flash->error(__('There has been an error while trying to flush queued emails, please try again.'));
    //     }

    //     return $this->redirect(['controller' => 'queue', 'action' => 'index', '?' => ['advanced_filter' => 1]]);
    // }

}