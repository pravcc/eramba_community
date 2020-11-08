<?php
App::uses('ModelBehavior', 'Model/Behavior');

/**
 * @tmp
 */
class DisplayFilterFieldsBehavior extends ModelBehavior
{
    public function setup(Model $Model, $settings = array()) {
        if (!isset($this->settings[$Model->alias])) {
            $this->settings[$Model->alias] = array();
        }
        $this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], $settings);
    }

    public function getDisplayFilterFields(Model $Model)
	{
		$fields = [];

		if (!empty($Model->displayField) && !empty($Model->filterArgs[$Model->displayField])) {
			$fields[] = $Model->displayField;
		}

		return $fields;
	}
}
