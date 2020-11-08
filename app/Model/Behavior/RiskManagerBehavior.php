<?php
App::uses('ModelBehavior', 'Model/Behavior');

class RiskManagerBehavior extends ModelBehavior {

	/**
	 * Get Procedures of a risk ids.
	 */
	public function getSecurityPolicyIncidents(Model $Model, $id) {
		$assoc = $Model->getAssociated('SecurityPolicyIncident');

		$with = $assoc['with'];
		$conds = $assoc['conditions'];
		$conds[$with . '.' . $assoc['foreignKey']] = $id;

		$Model->{$with}->bindModel(array(
			'belongsTo' => array(
				$assoc['className']
			)
		));

		$data = $Model->{$with}->find('all', array(
			'conditions' => $conds,
			'group' => $with . '.' . $assoc['associationForeignKey']
		));
		
		return $data;
	}

	public function getSecurityServiceIds(Model $Model, $riskIds = array()) {
		$assoc = $Model->getAssociated('SecurityService');

		$with = $assoc['with'];

		$controlIds = $Model->{$with}->find('list', array(
			'conditions' => array(
				$with . '.' . $assoc['foreignKey'] => $riskIds
			),
			'fields' => array(
				$with . '.' . $assoc['associationForeignKey']
			)
		));

		return array_values($controlIds);
	}

}
