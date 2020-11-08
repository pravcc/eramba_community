<?php
App::uses('Component', 'Controller');
App::uses('User', 'Model');
App::uses('Portal', 'Model');

class BruteForceComponent extends Component
{
	public $components = ['Session'];

    // portal id will be attached to logs
	public $settings = [
		'portal' => null
	];

    /**
     * Subject user of brute force check.
     */
    protected $_subjectUser = null;

	public function __construct(ComponentCollection $collection, $settings = [])
	{
        if (empty($this->settings)) {
            $this->settings = [];
        }
        $settings = array_merge($this->settings, (array)$settings);

        parent::__construct($collection, $settings);
    }

    public function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Check if login attempt is not a brute force attempt.
     * 
     * @return boolean
     */
    public function check()
    {
        // check only post, put request type
    	if (!$this->controller->request->is(['post', 'put'])) {
    		return true;
    	}

        // find user
        $user = $this->_getUser();

        // if the user was not found, we have no subject of next actions so we have nothing to do
        if (empty($user)) {
            return true;
        }

        // check if user has active ban
    	$incident = $this->_hasUserActiveBan($user);

        // if user does not have active ban check if login attempt exceeded limit
    	if (!$incident && $this->_loginAttemptExceeded($user)) {
            // ban user
            $this->_banUser($user);
    		$incident = true;
    	}

        // if attempt was blocked log this incident
    	if ($incident) {
    		$this->_logIncident($user);
    	}

    	return !$incident;
    }

    /**
     * Find user by request login.
     * 
     * @return array User.
     */
    protected function _getUser()
    {
        if ($this->_subjectUser === null) {
            $this->_subjectUser = ClassRegistry::init('User')->getUserByLogin($this->_getRequestLogin());
        }

        return $this->_subjectUser;
    }

    /**
     * Get sanitized login from request data.
     * 
     * @return string Login.
     */
    public function _getRequestLogin()
    {
        return isset($this->controller->request->data['User']['login']) ? trim($this->controller->request->data['User']['login']) : "";
    }

    /**
     * Check if user has active.
     *
     * @param array User.
     * @return boolean
     */
    protected function _hasUserActiveBan($user)
    {
        $result = ClassRegistry::init('UserBan')->find('count', [
            'conditions' => [
                'UserBan.until >' => date('Y-m-d H:i:s'),
                'UserBan.user_id' => $user['User']['id']
            ],
            'recursive' => -1
        ]);

        return (boolean) $result;
    }

    /**
     * Check if login attempt exceeded maximum allowed limit of logins in time.
     *
     * @param array User.
     * @return boolean
     */
    protected function _loginAttemptExceeded($user)
    {
        $limit = Configure::read('Eramba.Settings.BRUTEFORCE_WRONG_LOGINS');

        $lastLogs = ClassRegistry::init('SystemLogs.SystemLog')->find('list', [
            'conditions' => [
                'SystemLog.model' => 'User',
                'SystemLog.user_id' => $user['User']['id'],
                'SystemLog.action' => [User::SYSTEM_LOG_LOGIN_SUCCESS, User::SYSTEM_LOG_LOGIN_FAIL],
                'SystemLog.created > ' => date('Y-m-d H:i:s', strtotime('-' . Configure::read('Eramba.Settings.BRUTEFORCE_SECONDS_AGO') . 'seconds')),
            ],
            'fields' => ['SystemLog.action'],
            'limit' => $limit,
            'order' => ['SystemLog.created' => 'DESC']
        ]);

        return count($lastLogs) == $limit && !in_array(User::SYSTEM_LOG_LOGIN_SUCCESS, $lastLogs);
    }

    /**
     * Create a ban for user.
     *
     * @param array User.
     * @return boolean
     */
    protected function _banUser($user)
    {
        return (boolean) ClassRegistry::init('UserBan')->createBan($user['User']['id']);
    }

    /**
     * Log blocked login attempt.
     *
     * @param array User.
     * @return void
     */
    protected function _logIncident($user)
    {
    	ClassRegistry::init('User')->createSystemLog(User::SYSTEM_LOG_LOGIN_BRUTE_FORCE_BLOCK)
			->result($user['User']['login'])
			->subSubject(ClassRegistry::init('Portal'), $this->settings['portal'])
			->message([$user['User']['login']])
            ->userId($user['User']['id'])
			->log();
    }

    /**
     * Get error message with remaining ban time.
     * 
     * @return string
     */
    public function getMessage()
    {
    	$message = __('You are not allowed to login.');

    	$user = $this->_getUser();

		if (empty($user)) {
			return $message;
		}

    	$ban = ClassRegistry::init('UserBan')->getActiveBan($user['User']['id']);

    	if (empty($ban)) {
			return $message;
		}

		return __('This account has had too many wrong login attempts, we have blocked further logins for at most %s minutes.', 
			date('i', (strtotime($ban['UserBan']['until']) - time()) + 60)
		);
    }
}
