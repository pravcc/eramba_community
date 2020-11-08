<?php
App::uses('DashboardException', 'Dashboard.Error');
App::uses('CakeLog', 'Log');

// Dashboard KPI object
class VisualizedKpi extends DashboardKpiObject {
	
	/**
	 * Initialize the class.
	 */
	public function __construct(Dashboard $Dashboard, $data) {
		parent::__construct($Dashboard, $data);
	}

	protected function _buildResultQuery() {
		if (parent::_buildResultQuery() === false) {
			return false;
		}

		if (!$this->Model->Behaviors->enabled('Visualisation.Visualisation')) {
			return true;
		}

		$attributesList = Hash::combine($this->_data['DashboardKpiAttribute'], '{n}.model', '{n}.foreign_key');
		if (isset($attributesList['CustomRoles.CustomUser'])) {
			$loggedId = $attributesList['CustomRoles.CustomUser'];
		}

		$VisualisationSettingsUser = ClassRegistry::init('Visualisation.VisualisationSettingsUser');
		$modelJoins = $VisualisationSettingsUser->getJoins();

		$CustomRolesUsers = ClassRegistry::init('CustomRoles.CustomRolesUsers');
		$CustomRolesUsers->initAcl();

		$VisualisationShareUser = ClassRegistry::init('Visualisation.VisualisationShareUser');
		$objectJoins = $VisualisationShareUser->getJoins();

		$modelJoins[] = $objectJoins[] = [
				'table' => 'aros',
				'alias' => 'Aro',
				'type' => 'INNER',
				'conditions' => [
					'Permission.aro_id = Aro.id'
				]
		];

		$query = [
			'conditions' => [
				'Aco.model' => $this->Model->alias,
				'Aro.model' => 'User',
				'Aro.foreign_key' => $loggedId,
				'Permission._read' => 1
			],
			'fields' => ['Aco.foreign_key'],
			'recursive' => -1
		];

		$modelQuery = $query + ['joins' => $modelJoins];
		$modelQuery['conditions']['Aco.foreign_key'] = null;

		// if user is exempted for a section we skip this
		$modelNodes = $VisualisationSettingsUser->find('list', $modelQuery);
		if ($modelNodes) {
			return true;
		}
		
		$query['joins'] = $objectJoins;
		unset($query['conditions']['Aco.foreign_key']);
		$query['conditions']['Aco.foreign_key !='] = null;

		// $objectNodes = $VisualisationShare->find('list', $query);
		$VisualisationShareUser->Behaviors->load('Search.Searchable');
		$VisualisationShareUser->initAcl();

		// $subquery = $VisualisationShare->getQuery('all', $query);
		$list = $VisualisationShareUser->find('list', $query);
		$controlled = $this->Model->getControlled();

		if (is_array($controlled) && count($controlled)) {
			foreach ($controlled as $id) {
				if (in_array($id, $list)) {
					continue;
				}

				// visualisation check if the user has access via Share button
				$check = $VisualisationShareUser->Acl->check([
					'Visualisation.VisualisationUser' => [
						'id' => $loggedId
					]
				], [
					$this->Model->alias => [
						$this->Model->primaryKey => $id
					]
				], 'read');

				$customRoleData = ClassRegistry::init('CustomRoles.CustomRolesUser')->find('first', [
					'conditions' => [
						'user_id' => $loggedId
					],
					'fields' => [
						'id'
					],
					'recursive' => -1
				]);

				$customRoleId = $customRoleData['CustomRolesUser']['id'];

				// custom roles check if the user has access via his Custom Roles within an object
				$check = $check || $CustomRolesUsers->Acl->check([
					'CustomRoles.CustomRolesUser' => [
						'id' => $customRoleId
					]
				], [
					$this->Model->alias => [
						$this->Model->primaryKey => $id
					]
				], 'read');

				if ($check === true) {
					$list[] = $id;
				}
			}
		}

		if ($list) {
			$this->resultQuery['conditions'][] = "{$this->primaryField} IN (" . (implode(',', $list)) . ")";
		}
	}
}
