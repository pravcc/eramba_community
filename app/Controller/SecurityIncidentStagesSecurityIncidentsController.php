<?php
App::uses('SecurityIncidentStagesSecurityIncident', 'Model');
App::uses('SecurityIncident', 'Model');

class SecurityIncidentStagesSecurityIncidentsController extends AppController {

	// public $helpers = ['UserFields.UserField'];
	public $components = [
		'Paginator', 'ObjectStatus.ObjectStatus', 'Search.Prg', 'AdvancedFilters',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
			],
			'listeners' => [
				'Api', 'ApiPagination', 'Widget.Widget',
				'.SubSection' => [
					'addButton' => false
				],
				'.ModuleDispatcher' => [
					'listeners' => [
						'Reports.Reports',
					]
				]
			]
		],
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
		$this->Crud->enable(['index', 'edit']);

		parent::beforeFilter();

		$this->title = __('Security Incident Stages');
		$this->subTitle = __('');
	}

	public function index($id = null) {
		$this->title = __('Security Incident Stages');
		$this->subTitle = __('');

        $this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		$this->Modals->changeConfig('footer.buttons.closeBtn.options.data-yjs-on-complete', '#main-content');
		$this->Modals->changeConfig('footer.buttons.closeBtn.options.data-yjs-on-modal-close', '@reload-parent');

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit a Security Incident Stage');
		$this->subTitle = __('');

		$this->Crud->on('afterSave', array($this, '_afterSave'));

		return $this->Crud->execute();
	}

	public function _afterSave(CakeEvent $e)
	{
		$subject = $e->subject;
		$id = $subject->id;
		$model = $subject->model;
		$success = $subject->success;

		if ($success) {
			// auto-close incident
			$data = $model->find('first', [
	    		'conditions' => [
	    			$model->alias . '.id' => $id
	    		],
	    		'recursive' => 0
	    	]);

	    	$securityIncidentId = $data[$model->alias]['security_incident_id'];
	    	$autoClose = $data['SecurityIncident']['auto_close_incident'];
	    	$isAlreadyClosed = $data['SecurityIncident']['security_incident_status_id'] == SecurityIncident::STATUS_CLOSED;

	    	$allCompleted = !$model->find('count', [
	    		'conditions' => [
					$model->alias . '.security_incident_id' => $securityIncidentId,
					$model->alias . '.status' => SecurityIncidentStagesSecurityIncident::STATUS_INITIATED
				],
				'recursive' => -1
			]);

	    	if ($allCompleted && $autoClose && !$isAlreadyClosed) {
	    		$save = (boolean) $model->SecurityIncident->updateAll(
					array(
						'SecurityIncident.security_incident_status_id' => SecurityIncident::STATUS_CLOSED,
						'SecurityIncident.closure_date' => 'NOW()',
						'SecurityIncident.modified' => 'NOW()',
					),
					array('SecurityIncident.id' => $securityIncidentId)
				);

				if ($save) {
					$this->Flash->primary(__('Incident was automatically closed'));
				}
	    	}

			// trigger status on related incident
			$model->SecurityIncident->triggerObjectStatus(null, [$securityIncidentId]);
		}
	}

}