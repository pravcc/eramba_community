<?php
App::uses('DashboardAttribute', 'Dashboard.Lib/Dashboard/Attribute');

class ObjectStatusDashboardAttribute extends DashboardAttribute {

	public function getLabel(Model $Model, $attribute) {
		$field = $Model->Behaviors->ObjectStatus->field($Model, $attribute);
		return $field['title'];
	}

	public function listAttributes(Model $Model) {
		if ($Model->Behaviors->loaded('ObjectStatus.ObjectStatus')) {
			return $Model->Behaviors->ObjectStatus->getObjectStatusFields($Model);
		}

		return [];
	}

	public function joinAttributes(Model $Model) {
		return [
			[
				'table' => 'object_status_object_statuses',
				'alias' => 'ObjectStatus',
				'type' => 'INNER',
				'conditions' => [
					'ObjectStatus.model' =>  $Model->alias,
					'ObjectStatus.foreign_key = ' . $Model->escapeField($Model->primaryKey),
					'ObjectStatus.status' => '1'
				]	
			]
		];
	}

	public function applyAttributes(Model $Model, $attribute) {
		return [
			'ObjectStatus.name' => $attribute
		];
	}

	public function buildQuery(Model $Model, $attribute) {
		return [
			'joins' => $this->joinAttributes($Model),
			'conditions' => $this->applyAttributes($Model, $attribute),
			'fields' => [$Model->escapeField($Model->primaryKey)]
		];
	}
}