<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');
App::uses('AuthComponent', 'Controller/Component');

class WidgetBehavior extends ModelBehavior
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

    public function widgetView(Model $Model, $foreignKey, $comments = true, $attachments = true)
    {
        $now = date('Y-m-d H:i:s');

        $ret = ClassRegistry::init('Widget.WidgetView')->createOrUpdateView(
            $Model->modelFullName(),
            $foreignKey,
            AuthComponent::user('id'),
            $now,
            ($comments) ? $now : null,
            ($attachments) ? $now : null
        );

        return $ret;
    }
}
