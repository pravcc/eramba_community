<?php
App::uses('AbstractTransport', 'Network/Email');

/**
 * Send mail using Queue plugin.
 *
 */
class QueueTransport extends AbstractTransport {

    protected static $queueId = null;

/**
 * Default queue limit.
 * @var integer
 */
    public static $queueLimit = 15;

    public function __construct() {
        if (self::$queueId === null) {
            self::setQueueId(self::generateQueueId());
        }
    }

/**
 * Send queue email.
 *
 * @param CakeEmail $email CakeEmail
 * @return array
 */
    public function send(CakeEmail $email) {
        if ($email->queueId() === null) {
            $email->queueId(self::$queueId);
        }

        $queue = ClassRegistry::init('Queue');
        $result = $queue->add($email);

        //turn off logs to prevent throwing warnings
        $email->config(['log' => false]);

        if (empty($result)) {
            return false;
        }

        return $result;
    }

/**
 * Set $queueId.
 * 
 * @return void 
 */
    public static function setQueueId($queueId) {
        self::$queueId = $queueId;
    }

/**
 * Generate unique $queueId string.
 * 
 * @return string 
 */
    public static function generateQueueId() {
        return sprintf('%s-%s', time(), uniqid());
    }


    public static function getQueueLimit() {
        $limit = static::$queueLimit;

        if (defined('QUEUE_TRANSPORT_LIMIT')) {
            $limit = ((int) QUEUE_TRANSPORT_LIMIT);
        }

        return $limit;
    }
}