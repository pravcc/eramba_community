<?php
App::uses('NewsAppModel', 'News.Model');

class News extends NewsAppModel
{
    public $actsAs = [
        'Acl' => [
            'type' => 'controlled'
        ],
        'AppNotification.AppNotification'
    ];

    public function newsExists($supportId)
    {
        return (bool) $this->find('count', [
            'conditions' => [
                'News.support_id' => $supportId
            ]
        ]);
    }
}
