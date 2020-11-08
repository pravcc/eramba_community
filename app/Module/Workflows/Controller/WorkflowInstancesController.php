<?php
/**
 * @package       Workflows.Controller
 */
 
App::uses('WorkflowsAppController', 'Workflows.Controller');
App::uses('WorkflowInstanceObject', 'Workflows.Lib');

class WorkflowInstancesController extends WorkflowsAppController {
	public $helpers = array('Html', 'Form', 'Ajax' => array('controller' => 'workflowInstances'));
	public $components = array('Session', 'Paginator', 'Ajax' => array(
		'actions' => array('call')
	));
	public $uses = array(
		'Workflows.WorkflowInstance',
		'Workflows.WorkflowStageStep',
		'Workflows.WorkflowAccess'
	);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow();
	}

	/**
	 * Management page for a single object instance.
	 */
	public function manage($model, $foreignKey) {
		$this->set('title_for_layout', __('Workflow Management'));
		$this->set('subtitle_for_layout', $this->getSubtitle($model, $foreignKey));
		$this->set('modelLabel', $this->getLabel($model));
		$this->set('objectTitle', $this->{$model}->getRecordTitle($foreignKey));

		$workflowsEnabled = $this->WorkflowInstance->WorkflowSetting->isEnabled($model);
		$this->set('workflowsEnabled', $workflowsEnabled);
		
		if ($workflowsEnabled === true) {
			$InstanceClass = $this->getInstance($model, $foreignKey);
			$this->set('Instance', $InstanceClass);

			$this->initOptions($model, $foreignKey);
			$log = $this->WorkflowInstance->WorkflowInstanceLog->find('all', [
				'conditions' => [
					'WorkflowInstanceLog.wf_instance_id' => $InstanceClass->WorkflowInstance->id
				],
				'order' => ['WorkflowInstanceLog.created' => 'DESC', 'WorkflowInstanceLog.type' => 'DESC'],
				'limit' => 20,
				'recursive' => -1
			]);
			$this->set('log', $log);
		}
	}

	protected function getInstance($model, $foreignKey) {
		return $this->WorkflowInstance->getInstance($model, $foreignKey);
	}

	/**
	 * Options needed for manage page.
	 */
	protected function initOptions($model, $foreignKey) {
		$FieldDataCollection = $this->WorkflowInstance->getFieldDataEntity();
		
		$this->set('FieldDataCollection', $FieldDataCollection);
		$this->set($FieldDataCollection->getViewOptions($model));

		$stages = $this->WorkflowInstance->WorkflowStage->findByModel($model);
		$this->set('stages', $stages);
		$this->set('model', $model);
		$this->set('foreignKey', $foreignKey);
	}

	/**
	 * Handles various requests to be applied on an instance.
	 */
	public function handleRequest($model, $foreignKey, $requestType, $stageId) {
		$this->set('showHeader', true);
		$this->set('title_for_layout', __('Workflow Request'));
		$requestSlug = Inflector::slug($requestType);

		$whitelist = [
			'force-stage',
			'call-stage',
			'approve-stage'
		];

		if (!in_array($requestType, array_keys($whitelist))) {
			throw new ForbiddenException(__('Requested action type is not valid.'));
		}
		$InstanceClass = $this->getInstance($model, $foreignKey);

		$accessList = [
			'force-stage' => $InstanceClass->isWorkflowOwner($this->logged['id']),
			'call-stage' => $InstanceClass->canCallStage($this->logged['id']),
			'approve-stage' => $InstanceClass->canApproveStage($this->logged['id'], $stageId),
		];

		$hasAccess = $accessList[$requestType];
		$this->set([
			'model' => $model,
			'foreignKey' => $foreignKey,
			'requestType' => $requestType,
			'stageId' => $stageId,
			'hasAccess' => $hasAccess
		]);

		if (!$hasAccess) {
			return true;
		}

		$this->initOptions($model, $foreignKey);

		if ($this->request->is('post')) {
			$dataSource = $this->WorkflowInstance->getDataSource();
			$dataSource->begin();

			$ret = $this->WorkflowInstance->{$requestSlug}($InstanceClass->WorkflowInstance->id, $stageId);

			if ($ret) {
				$dataSource->commit();
				$this->Ajax->success();	
				$this->Session->setFlash(__('Your request has been completed successfully.'), FLASH_OK);
			}
			else {
				$validationError = array_values($this->WorkflowInstance->requestErrors);

				$msg = __('Error occured, unable to complete your request at the moment.');
				if (isset($validationError[0][0])) {
					$msg = $validationError[0][0];
				}
				
				$dataSource->rollback();
				$this->Session->setFlash($msg, FLASH_ERROR);
			}
		}
		else {
		}
		
		$this->{$requestSlug}($stageId);
	}

	protected function call_stage($stageId) {
		$this->set('title_for_layout', __('Call Stage Request'));

		$data = $this->WorkflowInstance->WorkflowStage->getItem($stageId);
		$this->set('message', __('Are you sure you want to call stage "%s"?', $data['WorkflowStage']['name']));
	}

	protected function approve_stage($stageId) {
		$this->set('title_for_layout', __('Stage Approval'));

		$data = $this->WorkflowInstance->WorkflowStage->getItem($stageId);
		$this->set('message', __('Are you sure you want to approve stage "%s"?', $data['WorkflowStage']['name']));
	}

	protected function force_stage($stageId) {
		$this->set('title_for_layout', __('Force Stage'));

		$data = $this->WorkflowInstance->WorkflowStage->getItem($stageId);
		$this->set('message', __('Are you sure you want to force stage "%s"?', $data['WorkflowStage']['name']));
	}

	public function forceStageForm($model, $foreignKey) {
		$this->initOptions($model, $foreignKey);

		if ($this->request->is('post')) {
			$this->WorkflowInstance->set($this->request->data);
			if ($this->WorkflowInstance->validates(['fieldList' => ['wf_stage_id']])) {
				$this->WorkflowInstance->switchStage($this->WorkflowInstance->id, $this->WorkflowInstance->data['WorkflowInstance']['wf_stage_id']);
				$this->Ajax->success();	
				// $this->Session->setFlash(__('Your request has been completed successfully.'), FLASH_OK);
			}
			else {
				$this->Session->setFlash(__('Please, choose a stage and then try to continue'), FLASH_ERROR);
			}
		}

		$this->render('Workflows.../Elements/force_stage');
	}

}