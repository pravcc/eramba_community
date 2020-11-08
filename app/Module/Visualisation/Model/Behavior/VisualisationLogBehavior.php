<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');
App::uses('CakeText', 'Utility');

class VisualisationLogBehavior extends ModelBehavior {

/**
 * Default config
 *
 * @var array
 */
    protected $_defaults = [
    ];

    public $settings = [];

    private $_requestId = null;

    const SYSTEM_LOG_VISUALISATION_SHARED = 901;
    const SYSTEM_LOG_VISUALISATION_NOT_SHARED = 902;
    const SYSTEM_LOG_VISUALISATION_EXEMPTED = 903;
    const SYSTEM_LOG_VISUALISATION_NOT_EXEMPTED = 904;

/**
 * Setup
 *
 * @param Model $Model
 * @param array $settings
 * @throws RuntimeException
 * @return void
 */
    public function setup(Model $Model, $settings = []) {
        if (!isset($this->settings[$Model->alias])) {
            $this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
        }
    }

/**
 * Create visualisation inspect log of item.
 * 
 * @param  Model $Model
 * @param  boolean $add Determine if we are adding or removing users.
 * @param  array $object Shared object defined in format [0 => Model, 1 => foreign_key].
 * @param  array $users List of user ids.
 * @return boolean Success.
 */
    public function visualisationLog(Model $Model, $add, $object, $users) {
        $ret = true;

        $action = $this->_getLogAction($Model, $add);
        $SubModel = ClassRegistry::init($object[0]);
        $users = (array) $users;

        foreach ($users as $user) {
            $ret &= (boolean) $Model->createSystemLog($action)
                ->subSubject($SubModel, $object[1])
                ->result($user)
                ->log();
        }

        return $ret;
    }

/**
 * Get log action.
 */
    protected function _getLogAction(Model $Model, $add) {
        $exempted = in_array($Model->name, ['VisualisationSettingsUser', 'VisualisationSettingsGroup']);

        $action = null;
        
        if ($add && $exempted) {
            $action = self::SYSTEM_LOG_VISUALISATION_EXEMPTED;
        }
        elseif (!$add && $exempted) {
            $action = self::SYSTEM_LOG_VISUALISATION_NOT_EXEMPTED;
        }
        elseif ($add && !$exempted) {
            $action = self::SYSTEM_LOG_VISUALISATION_SHARED;
        }
        else {
            $action = self::SYSTEM_LOG_VISUALISATION_NOT_SHARED;
        }

        return $action;
    }

/**
 * Check if visualisation notification should be send.
 * 
 * @param  Model $Model
 * @param  array $object Shared object defined in format [0 => Model, 1 => foreign_key].
 * @param  array $user User id.
 * @return boolean
 */
    public function isReadyToNotify(Model $Model, $object, $user) {
        $SystemLog = ClassRegistry::init('SystemLogs.SystemLog');

        $exempted = in_array($Model->name, ['VisualisationSettingsUser', 'VisualisationSettingsGroup']);
        if ($exempted) {
            $actions = [
                self::SYSTEM_LOG_VISUALISATION_EXEMPTED,
                self::SYSTEM_LOG_VISUALISATION_NOT_EXEMPTED,
            ];
        }
        else {
            $actions = [
                self::SYSTEM_LOG_VISUALISATION_SHARED,
                self::SYSTEM_LOG_VISUALISATION_NOT_SHARED,
            ];
        }

        $data = $SystemLog->find('all', [
            'conditions' => [
                'SystemLog.model' => $Model->modelFullName(),
                'SystemLog.action' => $actions,
                'SystemLog.sub_model' => $object[0],
                'SystemLog.sub_foreign_key' => $object[1],
                'SystemLog.result' => $user,
            ],
            'fields' => [
                'SystemLog.action', 'SystemLog.request_id',
            ],
            'order' => ['SystemLog.id' => 'DESC'],
            'limit' => 2,
            'recursive' => -1
        ]);

        if (empty($data)) {
            return true;
        }

        //last log must be - user was added to share
        $lastLogAddType = in_array($data[0]['SystemLog']['action'], [self::SYSTEM_LOG_VISUALISATION_EXEMPTED, self::SYSTEM_LOG_VISUALISATION_SHARED]);

        //previous log must be - user was removed from share, or log doesnt exist
        $previousLogRemoveType = empty($data[1]) || in_array($data[1]['SystemLog']['action'], [self::SYSTEM_LOG_VISUALISATION_NOT_EXEMPTED, self::SYSTEM_LOG_VISUALISATION_NOT_SHARED]);

        //last and previous logs must be from different requests
        $differentRequests = empty($data[1]) || $data[0]['SystemLog']['request_id'] !== $data[1]['SystemLog']['request_id'];

        return ($lastLogAddType && $previousLogRemoveType && $differentRequests);
    }

    public static function getVisualisationLogsConfig() {
        return [
            'logs' => [
                self::SYSTEM_LOG_VISUALISATION_SHARED => [
                    'action' => self::SYSTEM_LOG_VISUALISATION_SHARED,
                    'label' => __('Object shared'),
                    'message' => __('Object shared.')
                ],
                self::SYSTEM_LOG_VISUALISATION_NOT_SHARED => [
                    'action' => self::SYSTEM_LOG_VISUALISATION_NOT_SHARED,
                    'label' => __('Object no longer shared'),
                    'message' => __('Object no longer shared.')
                ],
                self::SYSTEM_LOG_VISUALISATION_EXEMPTED => [
                    'action' => self::SYSTEM_LOG_VISUALISATION_EXEMPTED,
                    'label' => __('Object exempted'),
                    'message' => __('Object exempted.')
                ],
                self::SYSTEM_LOG_VISUALISATION_NOT_EXEMPTED => [
                    'action' => self::SYSTEM_LOG_VISUALISATION_NOT_EXEMPTED,
                    'label' => __('Object no longer exempted'),
                    'message' => __('Object no longer exempted.')
                ],
            ],
        ];
    }

}
