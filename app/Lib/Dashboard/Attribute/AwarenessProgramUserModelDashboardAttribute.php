<?php
App::uses('DashboardAttribute', 'Dashboard.Lib/Dashboard/Attribute');

class AwarenessProgramUserModelDashboardAttribute extends DashboardAttribute {

	public function getLabel(Model $Model, $attribute = null) {
		$labels = [
			'ActiveUser' => __('Users in the Program'),
			'IgnoredUser' => __('Users Excluded'),
			'CompliantUser' => __('Compliant Users'),
			'NotCompliantUser' => __('Not compliant users')
		];

		if ($attribute === null) {
			return $labels;
		}

		return $labels[$attribute];
	}

	public function buildUrl(Model $Model, &$query, $attribute, $item = []) {
		return $query;
	}

	public function listAttributes(Model $Model) {
		return array_keys($this->getLabel($Model));
	}

	public function buildQuery(Model $Model, $attribute) {
		return [];
	}
}