<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');
App::uses('SystemLog', 'SystemLogs.Model');
App::uses('Router', 'Routing');
App::uses('CakeText', 'Utility');
App::uses('AuthComponent', 'Controller/Component');
App::uses('SystemLogBuilder', 'SystemLogs.Lib');

class SystemLogsBehavior extends ModelBehavior
{
    /**
     * Default config
     *
     * @var array
     */
    protected $_defaults = [
        'modelClass' => 'SystemLogs.SystemLog'
    ];

    public $settings = [];

    private $_requestId = null;

    /**
     * Setup
     *
     * @param Model $Model
     * @param array $settings
     * @throws RuntimeException
     * @return void
     */
    public function setup(Model $Model, $settings = [])
    {
        if (!isset($this->settings[$Model->alias])) {
            $this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
        }

        $this->_loadLogsSettings($Model);
    }

    /**
     * Load logs settings from model.
     * 
     * @param Model $Model
     * @return void
     */
    protected function _loadLogsSettings(Model $Model)
    {
        if (!$Model->hasMethod('getSystemLogsConfig')) {
            return trigger_error('SystemLogs: Model %s is missing system logs configuration when loading it up.', $Model->alias);
        }

        $this->settings[$Model->alias] = Hash::merge($this->settings[$Model->alias], $Model->getSystemLogsConfig());
    }

    /**
     * Get log config.
     * 
     * @param  Model $Model
     * @param  int $action Action.
     * @return array Log config.
     */
    public function logConfig(Model $Model, $action = null)
    {
        if ($action !== null) {
            return $this->settings[$Model->alias]['logs'][$action];
        }
        return $this->settings[$Model->alias]['logs'];
    }

    /**
     * Create SystemLogBuilder builder instance.
     * 
     * @param  Model $Model
     * @param  int $action Action.
     * @param  int $foreignKey Related item id.
     * @param  mixed $result Result data.
     * @param  mixed $message Message or message params.
     * @param  Model $SubModel Secondary subject model.
     * @param  int $subForeignKey Secondary item id.
     * @param  int $userId User id.
     * @return SystemLogBuilder
     */
    public function createSystemLog(Model $Model, $action = null, $foreignKey = null, $result = null, $message = null, $SubModel = null, $subForeignKey = null, $userId = null)
    {
        $log = new SystemLogBuilder(
            $Model,
            $action,
            $foreignKey,
            $result,
            $message,
            $SubModel,
            $subForeignKey,
            $userId
        );

        return $log;
    }

    /**
     * Create log of system event.
     * 
     * @param  Model $Model
     * @param  int $action Action.
     * @param  int $foreignKey Related item id.
     * @param  mixed $result Result data.
     * @param  mixed $message Message or message params.
     * @param  Model $SubModel Secondary subject model.
     * @param  int $subForeignKey Secondary item id.
     * @param  int $userId User id.
     * @return boolean Success.
     */
    public function systemLog(Model $Model, $action, $foreignKey = null, $result = null, $message = null, $SubModel = null, $subForeignKey = null, $userId = null)
    {
        $SystemLog = ClassRegistry::init('SystemLogs.SystemLog');

        $request = Router::getRequest();

        $data = [
            'model' => $Model->modelFullName(),
            'foreign_key' => $foreignKey,
            'sub_model' => (!empty($SubModel)) ? $SubModel->modelFullName() : null,
            'sub_foreign_key' => $subForeignKey,
            'action' => $action,
            'result' => self::encodeResultData($result),
            'message' => $this->getMessage($Model, $action, $message),
            'user_model' => 'User', //this should be done more DRY in future
            'user_id' => ($userId !== null) ? $userId : AuthComponent::user('id'),
            'ip' => (!empty($request)) ? $request->clientIp() : '',
            'uri' => (!empty($request)) ? $request->here() : '',
            'request_id' => $this->_getRequestId(),
        ];

        $SystemLog->create();

        return ((boolean) $SystemLog->save($data));
    }

    public function bindSystemLog($Model)
    {
        if ($Model->getAssociated('SystemLog') === null) {
            list($plugin, $name) = pluginSplit($this->settings[$Model->alias]['modelClass']);

            $Model->bindModel([
                'hasMany' => [
                    $name => [
                        'className' => $this->settings[$Model->alias]['modelClass'],
                        'foreignKey' => 'foreign_key',
                        'conditions' => [
                            "{$name}.model" => (!empty($Model->plugin)) ? "{$Model->plugin}.{$Model->name}" : $Model->name,
                        ],
                    ]
                ]
            ], false);
        }
    }

    /**
     * Transfer data to string.
     * 
     * @param  mixed $result Action result data.
     * @return string String action result data.
     */
    public static function encodeResultData($result)
    {
        if (is_array($result) || is_object($result)) {
            $result = json_encode($result);
        }
        elseif ($result === false) {
            $result = 0;
        }

        return $result;
    }

    /**
     * Get log message with replaced message params.
     * 
     * @param  Model $Model
     * @param  int $action Action.
     * @param  mixed $message Message or message params in array.
     * @return string String Message.
     */
    public function getMessage($Model, $action, $message)
    {
        $resultMessage = $message;
        $logConfig = $this->logConfig($Model, $action);

        if ($message === null && !empty($logConfig['message'])) {
            $resultMessage = $logConfig['message'];
        }
        elseif (is_array($message) && !empty($logConfig['message'])) {
            $resultMessage = vsprintf($logConfig['message'], $message);
        }

        return $resultMessage;
    }

    /**
     * Transfer data to string.
     * 
     * @return return string Unique result data.
     */
    private function _getRequestId()
    {
        if ($this->_requestId === null) {
            $this->_requestId = CakeText::uuid();
        }

        return $this->_requestId;
    }

    /**
     * Get list of all log actions in model.
     * 
     * @param  Model $Model
     * @param  int $action Action.
     * @return array List of log actions.
     */
    public function listSystemLogActions($Model)
    {
        $config = $this->logConfig($Model);
        return Hash::combine($config, '{n}.action', '{n}.label');
    }
}
