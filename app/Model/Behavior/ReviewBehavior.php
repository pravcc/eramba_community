<?php
App::uses('ModelBehavior', 'Model/Behavior');

/**
 * @deprecated
 */
class ReviewBehavior extends ModelBehavior {

    public function setup(Model $Model, $settings = array()) {
        if (!isset($this->settings[$Model->alias])) {
            $this->settings[$Model->alias] = array();
        }
        $this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], $settings);
    }

    public function beforeFind(Model $Model, $query) {
        $query['conditions'][$Model->alias . '.model'] = $this->settings[$Model->alias]['review_model'];
        return $query;
    }
}
