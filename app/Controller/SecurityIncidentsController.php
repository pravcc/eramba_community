<?php
App::uses('AppController', 'Controller');
App::uses('SecurityIncidentStagesSecurityIncident', 'Model');

/**
 * @section
 */
class SecurityIncidentsController extends AppController
{
	public $helpers = ['UserFields.UserField'];
	public $components = [
		'Search.Prg', 'AdvancedFilters', 'Paginator', 'Pdf', 'ObjectStatus.ObjectStatus',
		//'Visualisation.Visualisation',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				],
			],
			'listeners' => [
				'Api', 'ApiPagination', 'BulkActions.BulkActions', 'Widget.Widget',
				'Visualisation.Visualisation',
				'Taggable.Taggable' => [
					'fields' => [
						'Classification'
					]
				],
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem',
						'CustomFields.CustomFields',
						'Reports.Reports',
					]
				]
			]
		],
		'UserFields.UserFields' => [
			'fields' => ['Owner']
		]
	];

	protected $_appControllerConfig = [
		'components' => [
		],
		'helpers' => [
		],
		'elements' => [
		]
	];

	public function beforeFilter() {
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'trash', 'history', 'restore']);

		parent::beforeFilter();

		$this->title = __('Security Incidents');
		$this->subTitle = __('Record and manage your program security incidents. Incidents can be linked to controls, assets and third parties in order to make it clear what components of the program have been affected.');

		$this->Security->unlockedActions = array('getAssets', 'getThirdParties');
		$this->set('appetiteMethod', ClassRegistry::init('RiskAppetite')->getCurrentType());
	}

	public function _afterPaginate(CakeEvent $event) {
		$event->subject->items = $this->setAffectedItems($event->subject->items);
		$event->subject->items = $this->setRisks($event->subject->items);
	}

	public function index() {
		// $filterConditions = $this->SecurityIncident->parseCriteria($this->Prg->parsedParams());
		// if (!empty($filterConditions) && empty($this->request->query['advanced_filter'])) {
		// 	$this->Paginator->settings['conditions'] = $filterConditions;
		// }

		// if (empty($this->request->query['advanced_filter'])) {
		// 	$this->Crud->on('afterPaginate', array($this, '_afterPaginate'));
		// }

		// $this->initOptions();
		
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	// private function setRisks($data) {
	// 	foreach ($data as $key => $item) {
	// 		$data[$key]['AssociatedRisks'] = $this->setRisk($item);
	// 	}

	// 	return $data;
	// }

	// private function setRisk($item) {
	// 	$assocRisks = array();
	// 	foreach ($item['AssetRisk'] as $risk) {
	// 		$risk['Containment'] = $this->getContainment($risk);
	// 		$assocRisks[] = $risk;
	// 	}
	// 	foreach ($item['ThirdPartyRisk'] as $risk) {
	// 		$risk['Containment'] = $this->getContainment($risk);
	// 		$assocRisks[] = $risk;
	// 	}
	// 	foreach ($item['BusinessContinuity'] as $risk) {
	// 		$risk['Containment'] = $this->getContainment($risk);
	// 		$assocRisks[] = $risk;
	// 	}

	// 	return $assocRisks;
	// }

	// private function getContainment($data) {
	// 	$containment = array();
	// 	foreach ($data['SecurityPolicy'] as $policy) {
	// 		$containment[] = $policy['index'];
	// 	}

	// 	return implode(', ', $containment);
	// }

	// private function setAffectedItems($data) {
	// 	foreach ($data as $key => $item) {
	// 		$data[$key]['Affected'] = $this->setAffectedItem($item);
	// 	}

	// 	return $data;
	// }

	// private function setAffectedItem($item) {
	// 	$affected = array();
	// 	foreach ($item['Asset'] as $asset) {
	// 		$legals = array();
	// 		foreach ($asset['Legal'] as $legal) {
	// 			$legals[] = $legal['name'];
	// 		}

	// 		$affected[] = array(
	// 			'type' => __('Asset'),
	// 			'name' => $asset['name'],
	// 			'liabilities' => !empty($legals) ? implode(', ', $legals) : '-'
	// 		);
	// 	}

	// 	foreach ($item['ThirdParty'] as $tp) {
	// 		$legals = array();
	// 		foreach ($tp['Legal'] as $legal) {
	// 			$legals[] = $legal['name'];
	// 		}

	// 		$affected[] = array(
	// 			'type' => __('Third Party'),
	// 			'name' => $tp['name'],
	// 			'liabilities' => !empty($legals) ? implode(', ', $legals) : '-'
	// 		);
	// 	}

	// 	return $affected;
	// }

	/**
	 * Lifecycle table data for a specific security incident.
	 */
	public function reloadLifecycle($id) {
		$this->allowOnlyAjax();
		$this->layout = false;

		$item = $this->SecurityIncident->find('first', array(
			'conditions' => array(
				'SecurityIncident.id' => $id
			),
			'contain' => $this->UserFields->attachFieldsToArray('Owner', $this->SecurityIncident->findContain)
			/*'contain' => array(
				'SecurityIncidentStage',
				'SecurityIncidentStagesSecurityIncident' => array(
					'Attachment',
					'Comment',
				)
			)*/
		));

		// we set this to fill in missing information
		$data = array($item);
		$data = $this->setAffectedItems($data);
		$data = $this->setRisks($data);

		$item = $data[0];

		$this->set('openStages', true);
		$this->set('item', $item);
		$this->render('../SecurityIncidents/item');
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Security Incident.');

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Security Incident.');
		$this->initAddEditSubtitle();

		$this->Crud->action()->saveOptions([
			'deep' => true
		]);
		$this->Crud->on('beforeSave', array($this, '_beforeSave'));
		$this->Crud->on('beforeSave', array($this, '_beforeAdd'));
		$this->Crud->on('beforeRender', [$this, '_beforeAddEditRender']);

		return $this->Crud->execute();
	}

	public function _beforeAdd(CakeEvent $e)
	{
		$subject = $e->subject;
		$request = $subject->request;
		$model = $subject->model;
		$stagesAlias = 'SecurityIncidentStagesSecurityIncident';
		
		// we associate stages for the created incident using saveAssociated
		$stages = $model->SecurityIncidentStage->getStagesList();
		if ($stages) {
			foreach ($stages as $key => $name) {
				$request->data[$stagesAlias][] = [
					$stagesAlias => [
						'security_incident_stage_id' => $key,
						'status' => SecurityIncidentStagesSecurityIncident::STATUS_INITIATED
					]
				];
			}
		}
	}

	public function edit( $id = null ) {
		$this->title = __('Edit a Security Incident.');
		$this->initAddEditSubtitle();

		$this->Crud->on('beforeSave', array($this, '_beforeSave'));
		$this->Crud->on('beforeRender', [$this, '_beforeAddEditRender']);

		return $this->Crud->execute();
	}

	public function _beforeSave(CakeEvent $e) {
		$model = $e->subject->model;

		if (isset($this->request->data[$model->alias]['Classification'])
			&& $this->request->data[$model->alias]['Classification'] === ''
		) {
			$this->request->data[$model->alias]['Classification'] = [];
		}
	}

	public function _beforeAddEditRender(CakeEvent $e)
	{
		$subject = $e->subject;
		$controller = $subject->controller;
		$mocel = $subject->model;

		$stages = $this->SecurityIncident->SecurityIncidentStage->getStagesList();
		$controller->set('stages', $stages);
		$controller->set('stagesExists', !empty($stages));
	}

	public function trash()
	{
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FiedData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	/**
	 * Initialize options for join elements.
	 */
	// private function initOptions() {
	// 	$classificationsTmp = $this->SecurityIncident->Classification->find('list', array(
	// 		'order' => array('Classification.name' => 'ASC'),
	// 		'fields' => array('Classification.id', 'Classification.name'),
	// 		'group' => array('Classification.name'),
	// 		'recursive' => -1
	// 	));
	// 	$classifications = array();
	// 	foreach ($classificationsTmp as $c) {
	// 		$classifications[] = $c;
	// 	}

	// 	$services = $this->SecurityIncident->SecurityService->find('list', array(
	// 		'conditions' => array(
	// 			'SecurityService.security_service_type_id !=' => SECURITY_SERVICE_DESIGN
	// 		),
	// 		'order' => array('SecurityService.name' => 'ASC'),
	// 		'recursive' => -1
	// 	));

	// 	$this->set('classifications', $classifications);
	// 	$this->set('services', $services);
	// 	$this->set('stages', $this->SecurityIncident->SecurityIncidentStage->getStagesList());
	// }

	private function initAddEditSubtitle() {
		$this->subTitle = __('Record and manage your program security incidents. Incidents can be linked to controls, assets and third parties in order to make it clear what components of the program have been affected.');
	}

	public function getAssets() {
		$this->YoonityJSConnector->deny();
		
		$this->allowOnlyAjax();
		$this->autoRender = false;

		$riskIds = json_decode($this->request->data['riskIds']);
		$data = $this->SecurityIncident->AssetRisk->getAssetIds($riskIds);

		echo json_encode($data);
	}

	public function getThirdParties() {
		$this->YoonityJSConnector->deny();

		$this->allowOnlyAjax();
		$this->autoRender = false;

		$riskIds = json_decode($this->request->data['riskIds']);
		$data = $this->SecurityIncident->ThirdPartyRisk->getThirdPartyIds($riskIds);

		echo json_encode($data);
	}

	public function getControls() {
		$this->YoonityJSConnector->deny();

		$this->allowOnlyAjax();
		$this->autoRender = false;

		$this->log($this->request->query);

		$pullData = array(
			'AssetRisk' => json_decode($this->request->query['riskIds']),
			'ThirdPartyRisk' => json_decode($this->request->query['tpRiskIds']),
			'BusinessContinuity' => json_decode($this->request->query['buRiskIds'])
		);

		$data = array();
		foreach ($pullData as $model => $ids) {
			$data = am($data, $this->SecurityIncident->{$model}->getSecurityServiceIds($ids));
		}

		$data = array_unique($data);

		echo json_encode($data);
	}

	public function getRiskProcedures() {
		$this->YoonityJSConnector->deny();

		$this->allowOnlyAjax();

		$riskIds = json_decode($this->request->query['riskIds']);
		$model = $this->request->query['model'];
		$data = $this->SecurityIncident->{$model}->getSecurityPolicyIncidents($riskIds);

		$this->set('data', $data);
	}

	public function history($id)
	{
		return $this->Crud->execute();
	}

	public function restore($autidId)
	{
		return $this->Crud->execute();
	}
}
