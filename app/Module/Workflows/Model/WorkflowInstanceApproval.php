<?php
/**
 * @package       Workflows.Model
 */

App::uses('WorkflowsAppModel', 'Workflows.Model');
App::uses('WorkflowInstanceRequest', 'Workflows.Model');
App::uses('WorkflowInstanceLog', 'Workflows.Model');

class WorkflowInstanceApproval extends WorkflowsAppModel {
	public $useTable = 'wf_instance_approvals';

	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'WorkflowInstanceRequest' => [
			'className' => 'Workflows.WorkflowInstanceRequest',
			'foreignKey' => 'wf_instance_request_id',
			'counterCache' => 'approvals_count'
		],
		'WorkflowStage' => [
			'className' => 'Workflows.WorkflowStage',
			'foreignKey' => 'wf_stage_id'
		],
		'User'
	);

	public $validate = [
		'wf_instance_request_id' => [
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			)
		],
		'user_id' => [
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			)
		],
		'status' => [
			'callable' => [
				'rule' => ['callbackValidation', ['WorkflowInstanceRequest', 'statuses']],
				'message' => 'Incorrect status'
			],
		]
	];

	public function afterSave($created, $options = []) {
		if (!$created) {
			return true;
		}

		// recalculate request completion
		$this->WorkflowInstanceRequest->processRequestCompletion($this->data[$this->alias]['wf_instance_request_id']);

		//log this approval
		$data = $this->getApprovalData($this->id);
		$this->WorkflowInstanceRequest->WorkflowInstance->WorkflowInstanceLog->add(
			$data['WorkflowInstanceRequest']['wf_instance_id'],
			WorkflowInstanceLog::TYPE_ADD_APPROVAL,
			sprintf(
				__('Approval for stage %s has been added by %s'),
				$data['WorkflowStage']['name'],
				$data['User']['full_name']
			)
		);
	}

	public function beforeValidate($options = array()) {
		$ret = true;

		// for a new created request, validate if there isnt any other request. if yes then deny adding new one.
		if (empty($this->id)) {
			if (!$ret &= $this->validateNoDuplicatedApproval($this->data[$this->alias]['wf_instance_request_id'])) {
				$this->invalidate('wf_instance_request_id', __('You already submitted approval for this request. It is possible to approve a single request only once.'));
			}
		}

		return $ret;
	}

	// general approval data
	public function getApprovalData($id) {
		return $this->find('first', [
			'conditions' => [
				'WorkflowInstanceApproval.id' => $id
			],
			'contain' => [
				'WorkflowInstanceRequest' => ['wf_instance_id'],
				'WorkflowStage' => ['name'],
				'User'
			]
		]);
	}

	// checks if user is not approving the same request second time
	public function validateNoDuplicatedApproval($instanceRequestId) {
		$currentUser = $this->currentUser();
		$conds = [
			$this->alias . '.wf_instance_request_id' => $instanceRequestId,
			$this->alias . '.user_id' => $currentUser['id'],			
		];

		// if (isset($this->data[$this->alias][$this->primaryKey])) {
		// 	$conds[$this->alias . '.' . $this->primaryKey . ' !='] = $this->data[$this->alias][$this->primaryKey];
		// }

		$count = $this->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return $count == 0;
	}

	/**
	 * Get the count of unique approvals for a certain request. Groupped by user_id.
	 * 
	 * @param  int|string $instanceRequestId Instance Request ID.
	 * @param  array  $otherConds            Additional conditions to query.
	 * @return int                           Count of records.
	 */
	public function getCount($instanceRequestId, array $otherConds = []) {
		$conds = [
			$this->alias . '.wf_instance_request_id' => $instanceRequestId,
		];

		if (!empty($otherConds)) {
			$conds = am($conds, $otherConds);
		}

		return $this->find('count', [
			'conditions' => $conds,
			'group' => [$this->alias . '.user_id'],
			'recursive' => -1
		]);
	}

	// adds a new request to specified instance
	public function addApproval($instanceId, $stageId) {
		$request = $this->WorkflowInstanceRequest->find('first', [
			'conditions' => [
				'WorkflowInstanceRequest.wf_instance_id' => $instanceId,
				'WorkflowInstanceRequest.wf_stage_id' => $stageId,
				'WorkflowInstanceRequest.status' => WorkflowInstanceRequest::STATUS_PENDING,
			],
			'recursive' => -1
		]);

		if (empty($request)) {
			throw new NotFoundException('Call request record for your approval not found.');
		}

		$currentUser = $this->currentUser();

		$this->create();
		$this->set([
			'wf_instance_request_id' => $request['WorkflowInstanceRequest']['id'],
			'wf_stage_id' => $request['WorkflowInstanceRequest']['wf_stage_id'],
			'user_id' => $currentUser['id']
		]);

		return $this->save();
	}

}