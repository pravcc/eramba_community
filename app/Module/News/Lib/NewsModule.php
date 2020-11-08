<?php
App::uses('ModuleBase', 'Lib');
App::uses('ErambaHttpSocket', 'Network/Http');

class NewsModule extends ModuleBase
{
    public function readNews()
    {
        if (Configure::read('Eramba.offline')) {
            return true;
        }

        $url = Configure::read('Eramba.SUPPORT_API_URL') . '/api/news';
        $query = [];

        $lastNews = $this->_getLastNews();
        if (!empty($lastNews)) {
            $query['upcoming_from'] = $lastNews['News']['support_id'];
        }

        $request = $this->_request($url, $query);

        if (!$request || !$request->isOk()) {
            return false;
        }

        $response = json_decode($request->body(), true);

        $news = $response['response'];

        foreach ($news as $item) {
            if (!ClassRegistry::init('News.News')->newsExists($item['id'])) {
                $this->_saveNews($item);
            }
        }

        return true;
    }

    private function _request($url, $query = [])
    {
        $clientId = CLIENT_ID;
        $clientKey = Configure::check('Eramba.Settings.CLIENT_KEY') ? Configure::read('Eramba.Settings.CLIENT_KEY') : false;

        $config = [
            'timeout' => 4,
            'ssl_verify_peer' => false,
            'timeout' => 15,
            'request' => [
                'header' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]
        ];

        $http = new ErambaHttpSocket($config);
        $http->configAuth('Basic', $clientId, $clientKey);

        return $http->get($url, $query);
    }

    private function _getLastNews()
    {
        return ClassRegistry::init('News.News')->find('first', [
            'order' => ['News.support_id' => 'DESC']
        ]);
    }

    private function _saveNews($data)
    {
        $News = ClassRegistry::init('News.News');

        $News->create();

        $ret = (bool) $News->save(['News' => [
            'support_id' => $data['id'],
            'title' => $data['title'],
            'content' => $data['content'],
            'date' => date('Y-m-d H:i:s', strtotime($data['created'])),
        ]]);

        if ($ret) {
            $ret &= (bool) $News->createAppNotification('News.NewsAppNotification')
                ->setTitle($data['title'])
                ->setForeignKey($News->id)
                ->save();
        }

        return $ret;
    }
}
