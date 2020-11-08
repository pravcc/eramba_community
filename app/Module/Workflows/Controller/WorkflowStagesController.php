<?php
/**
 * @package       Workflows.Controller
 */
 
App::uses('WorkflowsAppController', 'Workflows.Controller');
App::uses('WorkflowsModule', 'Workflows.Lib');

class WorkflowStagesController extends WorkflowsAppController {
	public $helpers = array('Html', 'Form', 'Ajax' => array('controller' => 'workflowStages'));
	public $components = array('Session', 'Paginator', 'SystemHealth', 'Ajax' => array(
		'actions' => array('add', 'edit', 'delete')
	));
	public $uses = array('Workflows.WorkflowStage', 'Workflows.WorkflowSetting');

	public function beforeFilter() {
		parent::beforeFilter();

		$hourlyCronStatus = $this->SystemHealth->cronsHourly();
		$this->set('hourlyCronStatus', $hourlyCronStatus);
	}

	/**
	 * We let user create and manage custom forms and custom fields for a certain section.
	 * 
	 * @param  string $model Model section.
	 */
	public function index($model) {
		$this->set('modelLabel', $this->getLabel($model));
		$this->set('title_for_layout', __('Workflow Management'));
		$this->set('subtitle_for_layout', $this->getLabel($model));
		
		$data = $this->WorkflowSetting->getItem($model);
		$this->set('setting', $data);

		// $this->request->data = $data;

		$this->set('title_for_layout', __('Workflows'));
		$this->set('subtitle_for_layout', __('TBD'));
		$this->set('model', $model);

		$backUrl = $this->getIndexUrl($model);
		$this->set('backUrl', $backUrl);

		$this->Paginator->settings['contain'] = [
			'ApprovalUser' => ['fields' => ['full_name']],
			'ApprovalGroup' => ['fields' => ['name']],
			'WorkflowStageStep' => [
				'WorkflowStageStepCondition',
				'CallUser'
			]
		];
		$this->Paginator->settings['order'] = [
			'WorkflowStage.stage_type' => 'ASC'
		];

		$this->Paginator->settings['conditions'] = [
			'WorkflowStage.model' => $model
		];

		$this->initOptions($model);
		$this->initIndex($model);

		return $this->Crud->execute();
	}

	protected function initIndex($model) {
		$FieldDataSettingsCollection = $this->WorkflowSetting->getFieldDataEntity();

		// FieldData collection is set to be managed in the view
		$this->set('FieldDataSettingsCollection', $FieldDataSettingsCollection);
		// By default all data needed for add/edit form is set through Field Data layer
		$this->set($FieldDataSettingsCollection->getViewOptions());

		$allStages = $this->WorkflowStage->find('all', [
			'conditions' => [
				'WorkflowStage.model' => $model
			],
			'recursive' => -1
		]);
		
		$_allStages = [];
		foreach ($allStages as $stage) {
			$_allStages[$stage['WorkflowStage']['id']] = $stage;
		}
		unset($allStages);
		$this->set('allStages', $_allStages);
	}

	public function delete($id = null) {
		$this->Crud->execute();
	}

	public function add($model = null) {
		$this->request->data['WorkflowStage']['model'] = $model;

		$this->initOptions($model);
		return $this->Crud->execute();
	}

	public function edit( $id = null ) {
		$this->set('edit', true);
		$this->Ajax->processEdit($id);
		
		$data = $this->WorkflowStage->find('first', array(
			'conditions' => array(
				'WorkflowStage.id' => $id
			)
		));
		
		if (empty($data)) {
			throw new NotFoundException();
		}

		$this->initOptions($data['WorkflowStage']['model']);
		return $this->Crud->execute();
	}

	protected function initOptions($model = null) {
		$FieldDataCollection = $this->WorkflowStage->getFieldDataEntity();
		
		$this->set('FieldDataCollection', $FieldDataCollection);
		// By default all data needed for add/edit form is set through Field Data layer
		$this->set($FieldDataCollection->getViewOptions());

		$this->set('model', $model);
	}

}
