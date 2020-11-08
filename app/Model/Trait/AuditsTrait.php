<?php
trait AuditsTrait
{
	public function lastAuditDate($id, $model, $result = array(1, null), $field = 'planned_date') {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
		$model = $model . 'Audit';
		$foreignKey = $this->hasMany[$model]['foreignKey'];

		$audit = $this->{$model}->find('first', array(
			'conditions' => array(
				$model . '.' . $foreignKey => $id,
				$model . '.planned_date <=' => $today,
				$model . '.result' => $result
			),
			'fields' => array($model . '.id', $model . '.result', $model . '.planned_date'),
			'order' => array($model . '.modified' => 'DESC'),
			'recursive' => -1
		));

		if (!empty($audit)) {
			return $audit[$model][$field];
		}

		return false;
	}

	public function lastMissingAudit($id, $model) {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
		$model = $model . 'Audit';
		$foreignKey = $this->hasMany[$model]['foreignKey'];

		$audit = $this->{$model}->find('first', array(
			'conditions' => array(
				$model . '.' . $foreignKey => $id,
				$model . '.planned_date <=' => $today,
				$model . '.result' => null
			),
			'order' => array($model . '.modified' => 'DESC'),
			'recursive' => -1
		));

		if (!empty($audit)) {
			$this->lastMissingAuditId = $audit[$model]['id'];
			return $audit[$model]['planned_date'];
		}

		return false;
	}

	public function lastMissingAuditResult($id, $model) {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
		$model = $model . 'Audit';
		$foreignKey = $this->hasMany[$model]['foreignKey'];

		$audit = $this->{$model}->find('first', array(
			'conditions' => array(
				$model . '.' . $foreignKey => $id,
				$model . '.planned_date <=' => $today,
				$model . '.result' => array(1,0)
			),
			'order' => array(/*$model . '.planned_date' => 'DESC', */$model . '.modified' => 'DESC'),
			'recursive' => -1
		));

		if (!empty($audit)) {
			if ($audit[$model]['result']) {
				return __('Pass');
			}

			return __('Fail');

		}

		return false;
	}
}