<?php
App::uses('ModelBehavior', 'Model');
App::uses('AppModel', 'Model');

class NotificationsSystemBehavior extends ModelBehavior {

	public function setup(Model $Model, $settings = array()) {
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = array(
				'includeNotifications' => true
			);
		}
		$this->settings[$Model->alias] = array_merge(
		$this->settings[$Model->alias], (array) $settings);
	}

	public function beforeFind(Model $Model, $query) {
		if ($this->settings[$Model->alias]['includeNotifications']) {
			$this->bindNotifications($Model);

			// @todo rewrite
			// $conds = $Model->mapping['notificationSystem'] === true || (is_array($Model->mapping['notificationSystem']) && in_array(AppModel::$currentAction, $Model->mapping['notificationSystem']));
			
			// @todo rewrite			
			// if ($Model->name == AppModel::$modelClass && $conds) {
			// 	$query = AppModel::includeToQuery($query, $Model->name, 'NotificationObject');
			// }
		}

		return $query;
	}

	/**
	 * Set whether notifications should be included or not.
	 */
	public function includeNotifications(Model $Model, $include = true) {
		if (!is_bool($include)) {
			$include = true;
		}
		
		$this->settings[$Model->alias]['includeNotifications'] = $include;
	}

}
