<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('ErambaCakeEmail', 'Network/Email');
App::uses('QueueTransport', 'Network/Email');
App::uses('AbstractTransport', 'Network/Email');
App::uses('Queue', 'Model');

/**
 * Test case
 */
class QueueTransportTest extends CakeTestCase {

    public $fixtures = array(
        'app.CakeSession', 'app.Setting', 'app.SettingGroup', 'app.User', 'app.Workflow', 
        'app.LdapConnectorAuthentication', 'app.LdapConnector', 'app.Queue'
    );

    public function setUp() {
        parent::setUp();
        $this->QueueTransport = new QueueTransport();
        $this->Queue = ClassRegistry::init('Queue');
    }

    public function testSend() {
        ErambaCakeEmail::enableQueue();

        $email = new ErambaCakeEmail();
        $email->from('noreply@eramba.org', 'Test');
        $email->to('test@eramba.org', 'Test');
        $email->subject('Testing Message');

        $result = $this->QueueTransport->send($email);

        $item = $this->Queue->find('first');

        $this->assertTrue(!empty($item));
        $this->assertEqual($item['Queue']['queue_id'], $email->getQueueId());

        ErambaCakeEmail::disableQueue();

        $item = $this->Queue->find('first');

        $this->assertNotEqual($item['Queue']['status'], Queue::STATUS_PENDING);
    }

}
