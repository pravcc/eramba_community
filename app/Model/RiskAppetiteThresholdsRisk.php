<?php
class RiskAppetiteThresholdsRisk extends AppModel {
	public $actsAs = array(
		'Containable',
	);

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}

	public function saveItem($Model, $foreignKey, $thresholdId, $type) {
		$conds = [
			'model' => $Model->alias,
			'foreign_key' => $foreignKey,
			'type' => $type
		];

		$data = $this->find('first', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		if (!empty($data)) {
			$conds['id'] = $data['RiskAppetiteThresholdsRisk']['id'];
		}

		$conds['risk_appetite_threshold_id'] = $thresholdId;

		$this->create();
		return $this->save($conds);
	}
	


}
