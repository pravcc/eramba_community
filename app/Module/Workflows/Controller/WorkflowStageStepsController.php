<?php
/**
 * @package       Workflows.Controller
 */
 
App::uses('WorkflowsAppController', 'Workflows.Controller');

class WorkflowStageStepsController extends WorkflowsAppController {
	public $helpers = array('Html', 'Form', 'Ajax' => array('controller' => 'WorkflowStageSteps'));
	public $components = array('Session', 'Paginator', 'Ajax' => array(
		'actions' => array('add', 'edit', 'delete'),
		'Crud.Crud' => [
			'actions' => [
				// The controller action 'add' will map to the AddCrudAction
				'add' => [
					'className' => 'AppAdd',
					// 'saveMethod' => 'saveAssociated'
				],
				// The controller action 'edit' will map to the EditCrudAction
				'edit' => 'AppEdit',
				// The controller action 'view' will map to the ViewCrudAction
				//'view'  => 'Crud.View'
				// The controller action 'delete' will map to the DeleteCrudAction
				'delete' => [
					'className' => 'AppDelete',
				]
			],
		]
	));
	public $uses = array('Workflows.WorkflowStageStep');

	public function beforeFilter() {
		$this->Auth->allow('addCondition');
		parent::beforeFilter();
	}

	public function delete($id = null) {
		$this->Crud->execute();
	}

	protected function validateSteps($workflowStageId, $stepType) {
		$arr = [WorkflowStageStep::STEP_TYPE_DEFAULT, WorkflowStageStep::STEP_TYPE_ROLLBACK];

		if (in_array($stepType, $arr)) {
			$count = $this->WorkflowStageStep->countStepTypes($workflowStageId, $stepType);
			return $count == 0;
		}

		return true;
	}

	public function add($workflowStageId, $stepType) {
		$stepsValid = $this->validateSteps($workflowStageId, $stepType);
		$this->set('stepsValid', $stepsValid);

		$this->Crud->action()->saveMethod('saveAssociated');

		$data = $this->WorkflowStageStep->WorkflowStage->find('first', array(
			'conditions' => array(
				'WorkflowStage.id' => $workflowStageId
			)
		));

		if (empty($data)) {
			throw new NotFoundException();
		} 

		$this->initOptions($workflowStageId, $stepType);
		return $this->Crud->execute();
	}

	public function edit($id) {
		$this->Crud->action()->saveMethod('saveAssociated');

		$this->set('edit', true);
		$this->Ajax->processEdit($id);
		
		$data = $this->WorkflowStageStep->find('first', array(
			'conditions' => array(
				'WorkflowStageStep.id' => $id
			)
		));

		if (empty($data)) {
			throw new NotFoundException();
		}

		$this->initOptions($data['WorkflowStage']['id'], $data['WorkflowStageStep']['step_type']);
		return $this->Crud->execute();
	}

	/**
	 * Method renders a single conditional fields for a connection between stages.
	 */
	public function addCondition($workflowStageId, $index = 0) {
		$this->layout = false;

		$this->set('index', $index);
		$this->initStageOptions($workflowStageId);
	}

	public function addConditionValue($model, $fieldName, $index) {
		$this->layout = false;

		$this->loadModel($model);
		$fieldData = $this->{$model}->getFieldDataEntity($fieldName);
		// $ConditionModel = $this->WorkflowStageStep->WorkflowStageStepCondition;

		$this->set('FieldDataValueEntry', $fieldData);
		$this->set('index', $index);
		$this->set('sectionModel', $model);
		// $this->set('model', $model);
		$this->initConditionalOptions();
	}

	protected function initConditionalOptions() {
		$FieldDataCondsCollection = $this->WorkflowStageStep->WorkflowStageStepCondition->getFieldDataEntity();
		$this->set('FieldDataCondsCollection', $FieldDataCondsCollection);
		$this->set($FieldDataCondsCollection->comparison_type->getViewOptions());
	}

	protected function initStageOptions($workflowStageId) {
		$this->set('workflowStageId', $workflowStageId);

		// lets get the related section model for generating conditional field values
		$stage = $this->WorkflowStageStep->WorkflowStage->getItem($workflowStageId);
		$model = $stage['WorkflowStage']['model'];
		$this->set('sectionModel', $model);
		// $this->set('model', $model);

		$this->initConditionalOptions();

		$FieldDataCondsCollection = $this->WorkflowStageStep->WorkflowStageStepCondition->getFieldDataEntity();
		$this->set($FieldDataCondsCollection->field->getViewOptions($model));

		return $model;
	}

	protected function initOptions($workflowStageId, $stepType) {
		$model = $this->initStageOptions($workflowStageId);

		$this->request->data['WorkflowStageStep']['wf_stage_id'] = $workflowStageId;
		$this->request->data['WorkflowStageStep']['step_type'] = $stepType;
		
		$this->set('stepType', $stepType);

		$FieldDataCollection = $this->WorkflowStageStep->getFieldDataEntity();
		
		$this->set('FieldDataCollection', $FieldDataCollection);
		// By default all data needed for add/edit form is set through Field Data layer
		$data = $FieldDataCollection->getViewOptions($model);
		
		// temporary solution for unsetting a stage ID from the select field list of next stage IDs
		unset($data['wfNextStages'][$workflowStageId]);
		$this->set($data);
	}

}
