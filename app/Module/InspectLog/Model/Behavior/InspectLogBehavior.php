<?php
App::uses('ModelBehavior', 'Model');
App::uses('InspectLog', 'InspectLog.Model');
App::uses('Hash', 'Utility');
App::uses('Router', 'Routing');
App::uses('CakeText', 'Utility');
App::uses('AuthComponent', 'Controller/Component');

class InspectLogBehavior extends ModelBehavior {

/**
 * Default config
 *
 * @var array
 */
    protected $_defaults = [
        
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
    public function setup(Model $Model, $settings = []) {
        if (!isset($this->settings[$Model->alias])) {
            $this->settings[$Model->alias] = Hash::merge($this->_defaults, $settings);
        }
    }

/**
 * Create widget inspect log of item.
 * 
 * @param  Model $Model
 * @param  int $foreignKey Related item id.
 * @param  int $action Action.
 * @return boolean Success.
 */
    public function inspectLogWidget(Model $Model, $foreignKey = null) {
        $ret = $Model->systemLog($Model::SYSTEM_LOG_WIDGET_VIEW, $foreignKey);

        Cache::delete($Model->modelFullName() . '_' . AuthComponent::user('id'), 'widget_data');
        Cache::delete($Model->alias . '_' . AuthComponent::user('id'), 'widget_data');

        return $ret;
    }

/**
 * Return last system logs (SYSTEM_LOG_WIDGET_VIEW) for given model.
 * 
 * @param  Model $Model
 * @return boolean Last system logs data.
 */
    public function getInspectLogWidgetLastData(Model $Model) {
        $SystemLog = ClassRegistry::init('SystemLogs.SystemLog');

        $data = $SystemLog->find('all', [
            'conditions' => [
                'SystemLog.model' => $Model->modelFullName(),
                'SystemLog.action' => $Model::SYSTEM_LOG_WIDGET_VIEW,
                'SystemLog.user_model' => 'User',
                'SystemLog.user_id' => AuthComponent::user('id'),
                'SystemLog.foreign_key IS NOT NULL',
            ],
            'fields' => [
                'SystemLog.foreign_key',
                'SystemLog.created',
                'max(SystemLog.created) as max_created'
            ],
            'group' => ['SystemLog.foreign_key'],
            'recursive' => -1
        ]);
        
        return Hash::combine($data, '{n}.SystemLog.foreign_key', '{n}.0.max_created');
    }
}
