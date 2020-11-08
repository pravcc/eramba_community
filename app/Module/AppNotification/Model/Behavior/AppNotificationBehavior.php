<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');
App::uses('AppNotificationBuilder', 'AppNotification.Lib');

class AppNotificationBehavior extends ModelBehavior
{
    /**
     * Default config.
     *
     * @var array
     */
    protected $_defaults = [
    ];

    public $settings = [];

    /**
     * Setup.
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
    }

    /**
     * Create AppNotificationBuilder with notification instance.
     * 
     * @param Model  $Model
     * @param string $className BaseAppNotification class name.
     * @return AppNotificationBuilder
     */
    public function createAppNotification(Model $Model, $className)
    {
        $builder = new AppNotificationBuilder($className);

        $builder->setModel($Model->modelFullName());

        return $builder;
    }

    /**
     * Check if notification exists.
     * 
     * @param string $className Notification class name.
     * @param int $foreignKey Subject id.
     * @param boolean $excludeExpired Exclude expired notifications from check.
     * @return boolean
     */
    public function appNotificationExists(Model $Model, $className, $foreignKey, $excludeExpired = false)
    {
        return (boolean) ClassRegistry::init('AppNotification.AppNotification')->appNotificationExists($className, $Model->modelFullName(), $foreignKey, $excludeExpired);
    }

    /**
     * Remove notification by subject model and foreign key.
     * 
     * @param string $className Notification class name.
     * @param int $foreignKey Subject id.
     * @return boolean
     */
    public function removeAppNotificationsBySubject(Model $Model, $className, $foreignKey)
    {
        return (boolean) ClassRegistry::init('AppNotification.AppNotification')->removeAppNotificationsBySubject($className, $Model->modelFullName(), $foreignKey);
    }
}
