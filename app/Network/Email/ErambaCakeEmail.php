<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('QueueTransport', 'Network/Email');
App::uses('Setting', 'Model');

/**
 * Eramba email class
 *
 */
class ErambaCakeEmail extends CakeEmail {

/**
 * Default constants for settings.
 */
    public static $defaults = [
        'SMTP_USE' => SMTP_USE,
        'SMTP_HOST' => SMTP_HOST,
        'USE_SSL' => USE_SSL,
        'SMTP_USER' => SMTP_USER,
        'SMTP_PWD' => SMTP_PWD,
        'SMTP_TIMEOUT' => SMTP_TIMEOUT,
        'SMTP_PORT' => SMTP_PORT,
        'EMAIL_NAME' => EMAIL_NAME,
        'NO_REPLY_EMAIL' => NO_REPLY_EMAIL
    ];

/**
 * What format should the email be sent in
 *
 * @var string
 */
    protected $_emailFormat = 'html';

/**
 * What method should the email be sent. Eramba default transport.
 *
 * @var string
 */
    protected $_transportName = 'Queue';

/**
 * QueueId property in case email has QueueTransport.
 * 
 * @var string|null
 */
    protected $_queueId = null;

/**
 * Send QueueTransport email instantly.
 * 
 * @var boolean
 */
    protected $_instantQueueSend = null;

/**
 * Configure a related model to this Email instance.
 * 
 * @var null|string
 */
    protected $_model = null;

/**
 * Configure a related foreign key to this Email instance.
 * 
 * @var null|string
 */
    protected $_foreignKey = null;

/**
 * Method acts as a handler to transform Eramba-given configuration into CakePHP accepted configuration.
 * 
 * @param  array  $settings Eramba provided configuration.
 * @return array            CakePHP accepted configuration.
 */
    public static function buildErambaConfig($settings = array(), $liveTransport = false) {
        // we provide the defaults
        $emailConfig = array(
            'charset' => 'utf-8',
            'headerCharset' => 'utf-8',
            'from' => 'noreply@domain.org',
        );

        // we use the default configuration only in case there is no customized config provided
        if (empty($settings)) {
            $settings = self::$defaults;
        }

        // here begins the transformation of the configs
        if (!empty($settings['NO_REPLY_EMAIL'])) {
            if (!empty($settings['EMAIL_NAME'])) {
                $emailConfig['from'] = array($settings['NO_REPLY_EMAIL'] => $settings['EMAIL_NAME']);
            }
            else {
                $emailConfig['from'] = $settings['NO_REPLY_EMAIL'];
            }
        }

        if ($settings['SMTP_USE'] == 1) {
            $emailConfig['host'] = $settings['SMTP_HOST'];

            // handler for a SMTP server that does not require authentication
            // @see SmtpTransport::_auth()
            if (!empty($settings['SMTP_USER'])) {
                $emailConfig['username'] = $settings['SMTP_USER'];
                $emailConfig['password'] = $settings['SMTP_PWD'];
            }

            $emailConfig['timeout'] = $settings['SMTP_TIMEOUT'];
            $emailConfig['port'] = $settings['SMTP_PORT'];
            
            if ($settings['USE_SSL'] == Setting::USE_SSL_SSL) {
                $host = 'ssl://' . $emailConfig['host'];
                $emailConfig['host'] = $host;
            }

            if ($settings['USE_SSL'] == Setting::USE_SSL_TLS) {
                $emailConfig['tls'] = true;
            }
        }

        //set live transport
        if ($liveTransport) {
            $emailConfig['transport'] = (!empty($settings['SMTP_USE'])) ? 'Smtp' : 'Mail';
        }

        // we return the configuration array for CakeEmail.
        return $emailConfig;
    }

    public function __construct($config = null) {
        // in case a default email is constructed it uses eramba email settings.
        if (empty($config) || $config == 'default') {
            $config = self::buildErambaConfig();
        }

        parent::__construct($config);

        $this->config(array(
            'log' => array(
                'level' => 'email'
            )
        ));
    }

/**
 * Reset all CakeEmail internal variables to be able to send out a new email.
 *
 * @return self
 */
    public function reset() {
        parent::reset();

        $this->_transportName = 'Queue';
        $this->_queueId = null;
        
        return $this;
    }

/**
 * Send pending email with given id.
 *
 * @param  string $emailId Email id in queue.
 * @return boolean Success.
 */
    public static function sendQueueItem($emailId) {
        $conditions = ($emailId !== null) ? ['Queue.id' => $emailId] : [];

        return self::executeSendQueue($conditions);
    }

/**
 * Send all pending emails in queue.
 *
 * @param  string $queueId Queue ID.
 * @return boolean Success.
 */
    public static function sendQueue($queueId = null) {
        $conditions = ($queueId !== null) ? ['Queue.queue_id' => $queueId] : [];

        return self::executeSendQueue($conditions);
    }

/**
 * Send all pending emails with given additional conditons.
 *
 * @param  array $conditions Additional conditions.
 * @return boolean Success.
 */
    public function executeSendQueue($conditions = []) {
        $queue = ClassRegistry::init('Queue');
        $data = $queue->getPending(QueueTransport::getQueueLimit(), $conditions);

        $success = true;

        foreach ($data as $item) {
            $email = Queue::getItemData($item['Queue']['queue_id'], $item['Queue']['id']);
            if (!$email) {
                $queue->markAsFileNotExists($item);
                $success = false;
                continue;
            }

            //set fresh email configuration
            $email->config(self::buildErambaConfig(null, true));
            
            if ($email->send()) {
                $queue->markAsSuccess($item);
            }
            else {
                $queue->markAsFailed($item);
                $success = false;
            }
        }

        return $success;
    }

/**
 * Get/set $queueId.
 * 
 * @param string|null $queueId
 * @return string QueueId.
 */
    public function queueId($queueId = null) {
        if ($queueId !== null) {
            $this->_queueId = $queueId;
        }
        
        return $this->_queueId;
    }

/**
 * Get/set _instantQueueSend property to send queued email instantly.
 * 
 * @param boolean $instantSend Instant email send toggle.
 * @return boolean _instantQueueSend property value.
 */
    public function instant($instantSend = null) {
        if ($instantSend !== null) {
            $this->_instantQueueSend = $instantSend;
        }
        
        return $this->_instantQueueSend;
    }

/**
 * send email
 */
    public function send($content = null) {
        $result = false;

        try {
            // debugging works with both - standard emails and also with Queue feature
            $this->configureDebug();

            if ($email = parent::send($content)) {
                $result = $email;

                //if we are in QueueTransport and we want to force send instantly
                if ($this->_transportName == 'Queue' && $this->_instantQueueSend) {
                    self::sendQueueItem($email);
                }
            }
        }
        catch (Exception $e) {
            // debug($e);
        }
        
        return $result;
    }

/**
 * Configures debug for the email in case debug is enabled.
 */
    protected function configureDebug() {
        $debug = Configure::read('Eramba.Settings.EMAIL_DEBUG');

        // this is along with DebugTransport also a case when disableQueue() call starts sending emails while being on email debug mode
        if ($this->_transportName != 'Queue' && !empty($debug)) {
            // numeric value 1 or '1'
            if (is_numeric($debug)) {
                return $this->transport('Debug');
            }
            // string value as email address that will recieve all emails
            else {
                $this->_to = $this->_cc = $this->_bcc = array();
                return $this->to($debug);
            }
        }

        return true;
    }

/**
 * Additional method to change the layout template only.
 */
    public function layout($layout = null) {
        if ($layout === null) {
            return $this->_layout;
        }

        $this->_layout = $layout;

        return $this;
    }

    public function model($model = null)
    {
        if ($model === null) {
            return $this->_model;
        }

        if (is_string($model)) {
            $this->_model = $model;
        }

        if ($model instanceof Model) {
            $this->_model = $model->modelFullName();
        }

        return $this;
    }

    public function foreignKey($foreignKey = null) {
        if ($foreignKey === null) {
            return $this->_foreignKey;
        }

        $this->_foreignKey = $foreignKey;

        return $this;
    }

}