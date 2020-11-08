<?php
App::uses('NewsAppController', 'News.Controller');

class NewsController extends NewsAppController {

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

        $this->Auth->authorize = false;

        $this->title = __('News');
        $this->subTitle = __('');
    }

    public function view($id)
    {
        $news = $this->News->find('first', [
            'conditions' => [
                'News.id' => $id
            ]
        ]);

        if (empty($news)) {
            throw new NotFoundException();
        }

        $this->Modals->init();
        $this->Modals->setHeaderHeading($news['News']['title']);

        $this->set('news', $news);
    }
}
