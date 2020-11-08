<?php
/**
 * @package       Workflows.Model
 */

App::uses('WorkflowsAppModel', 'Workflows.Model');
App::uses('WorkflowInstance', 'Workflows.Model');
App::uses('WorkflowInstanceLog', 'Workflows.Model');
App::uses('WorkflowsModule', 'Workflows.Lib');

class WorkflowInstanceRequest extends WorkflowsAppModel {
	public $useTable = 'wf_instance_requests';

	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'WorkflowInstance' => [
			'className' => 'Workflows.WorkflowInstance',
			'foreignKey' => 'wf_instance_id',
			'counterCache' => [
				'pending_requests' => [
					'WorkflowInstanceRequest.status' => self::STATUS_PENDING
				]
			]
		],
		'WorkflowStage' => [
			'className' => 'Workflows.WorkflowStage',
			'foreignKey' => 'wf_stage_id'
		],
		'WorkflowStageStep' => [
			'className' => 'Workflows.WorkflowStageStep',
			'foreignKey' => 'wf_stage_step_id'
		],
		'User'
	);

	public $hasMany = array(
		'WorkflowInstanceApproval' => [
			'className' => 'Workflows.WorkflowInstanceApproval',
			'foreignKey' => 'wf_instance_request_id'
		]
	);

	// single has one is in __construct()
	public $hasOne = [];

	public $validate = [
		'wf_instance_id' => [
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			),
			// 'validateUniqueRequests' => array(
			// 	'rule' => 'validateUniqueRequests',
			// 	'message' => 'There is already one active pending request'
			// ),
		],
		'user_id' => [
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			),
			// 'validateUserAccess' => array(
			// 	'rule' => 'validateUserAccess',
			// 	'message' => 'Current user account does not have access to this feature'
			// ),
		],
		'status' => [
			'callable' => [
				'rule' => ['callbackValidation', ['WorkflowInstanceRequest', 'statuses']],
				'message' => 'Incorrect status'
			],
		]
	];

	public function __construct($id = false, $table = null, $ds = null) {
		$this->hasOne['UserApproval'] = [
			'className' => 'Workflows.WorkflowInstanceApproval',
			'foreignKey' => 'wf_instance_request_id',
			'conditions' => [
				'UserApproval.user_id' => $this->currentUser('id')
			]
		];

		parent::__construct($id, $table, $ds);
	}

	public function beforeValidate($options = array()) {
		$ret = true;

		// for a new created request, validate if there isnt any other request. if yes then deny adding new one.
		if (empty($this->id)) {
			if (!$ret &= $this->validateNoPendingRequests($this->data[$this->alias]['wf_instance_id'])) {
				$this->invalidate('wf_instance_id', __('There is already one existing active pending request.'));
			}
		}

		return $ret;
	}

	// log the request
	public function afterSave($created, $options = []) {
		if (!$created || !isset($this->data['WorkflowInstanceRequest']['wf_instance_id'])) {
			return true;
		}

		$stepMsg = [
			WorkflowStageStep::STEP_TYPE_DEFAULT => __('Default stage %s has been called by %s'),
			WorkflowStageStep::STEP_TYPE_CONDITIONAL => __('Conditional stage %s has been triggered automatically'),
			WorkflowStageStep::STEP_TYPE_ROLLBACK => __('Rollback stage %s has been triggered automatically')
		];

		$data = $this->getRequestData($this->id);
		$this->WorkflowInstance->WorkflowInstanceLog->add(
			$this->data['WorkflowInstanceRequest']['wf_instance_id'],
			WorkflowInstanceLog::TYPE_ADD_REQUEST,
			sprintf(
				$stepMsg[$data['WorkflowStageStep']['step_type']],
				$data['WorkflowStage']['name'],
				$data['User']['full_name']
			)
		);
	}

	/**
	 * Checks if this is another request for an instance that already has a different request pending.
	 * If it is another request, deny it as there is only single request for instance available at the same time.
	 */
	public function validateNoPendingRequests($instanceId) {
		$conds = [
			$this->alias . '.wf_instance_id' => $instanceId,
			$this->alias . '.status' => self::STATUS_PENDING
		];

		$count = $this->find('count', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return $count == 0;
	}

	/**
	 * Changes the status.
	 */
	public function setStatus($id, $status) {
		$this->id = $id;
		$this->set([
			'status' => $status
		]);

		return $this->save(null, [
			'fieldList' => ['status']
		]);
	}

	/**
	 * Returns active requests waiting to be processed.
	 */
	public function hasPending($instanceId) {
		return $this->find('count', [
			'conditions' => [
				$this->alias . '.wf_instance_id' => $instanceId,
				$this->alias . '.status' => self::STATUS_PENDING
			],
			'recursive' => -1
		]);
	}

	/**
	 * Adds a new request for a specified $instanceId.
	 * 
	 * @param int $instanceId  		Instance ID.
	 * @param int $stageId     		Stage ID that is being requested.
	 * @param int $stageStepId
	 */
	public function addRequest($instanceId, $stageId, $stageStepId) {
		$ret = true;

		$currentUser = $this->currentUser();

		$this->create();
		$this->set([
			'wf_instance_id' => $instanceId,
			'wf_stage_id' => $stageId,
			'wf_stage_step_id' => $stageStepId,
			'user_id' => $currentUser['id'],
			'status' => self::STATUS_PENDING
		]);

		$ret &= $saveData = $this->save();
		$ret &= $this->addRequestNotify($this->id);

		return $ret;
	}

	/**
	 * Notify assigned users for a called stage.
	 * 
	 * @param  int $id             Instance request ID from which to proess notifications.
	 * @return bool                True on success, False otherwise.
	 */
	public function addRequestNotify($id) {
		$data = $this->getRequestData($id);

		// we need a model alias to check the user accesses
		$model = $data['WorkflowInstance']['model'];

		// read the user IDs
		$userIds = $this->getNewRequestNotificationUsers($model, $data['WorkflowStageStep']['id']);

		$userEmails = $this->User->getEmails($userIds);
		$email = (new WorkflowsModule())->email([
			'to' => $userEmails,
			'subject' => __('Call Request For A Stage: "%s"', $data['WorkflowStage']['name']),
			'template' => 'call_stage',
			'viewVars' => [
				'data' => $data
			]
		]);

		return $email->send();
	}

	/**
	 * Get user IDs that are supposed to be notified about a called stage.
	 *
	 * @param  string $model 		 Model name to read the data from.
	 * @param  int    $stageStepId   Step stage ID of the record where to read the access values.
	 * @return array                 User IDs
	 */
	public function getNewRequestNotificationUsers($model, $stageStepId) {
		// get the users via WorklowAccessObject easily
		$WorkflowAccessObject = ClassRegistry::init('Workflows.WorkflowAccess')->getAccess($model);

		$users = $WorkflowAccessObject->get(
			['WorkflowStageStep', $stageStepId],
			WorkflowAccess::ACCESS_NOTIFY
		);

		return $users;
	}

	/**
	 * After approval record is saved, we check if the entire request object is finally approved.
	 */
	public function validateRequestCompletion($id) {
		$data = $this->find('first', [
			'conditions' => [
				'WorkflowInstanceRequest.id' => $id
			],
			'fields' => [
				'WorkflowInstanceRequest.*',
				'WorkflowInstance.model',
				'WorkflowInstance.foreign_key',
				'WorkflowStage.approval_method'
			],
			'recursive' => 0
		]);

		// request that is no longer pending but with OK status, doesnt need to be validated
		if ($data['WorkflowInstanceRequest']['status'] == self::STATUS_OK) {
			return true;
		}

		$approvalMethod = $data['WorkflowStage']['approval_method'];
		// possible to use approvals_count counterCache but only for informational purposes
		// $approvalsCount = $data['WorkflowInstanceRequest']['approvals_count'];
		$approvalsCount = $this->WorkflowInstanceApproval->getCount($id);

		$possibleMethods = array_keys(WorkflowStage::approvalMethods());
		if (!in_array($approvalMethod, $possibleMethods)) {
			trigger_error(__('The Workflow Request that is trying to get approved has a non-existent approval method selected!'));
			return false;
		}

		if ($approvalMethod == WorkflowStage::METHOD_SINGLE) {
			// nested IF condition, to not continue the process and load up WorkflowInstanceObject
			// and Accesses (below) when its not needed
			if ($approvalsCount >= 1) {
				return true;
			}

			return false;
		}

		$instanceData = $data['WorkflowInstance'];
		$InstanceClass = $this->WorkflowInstance->getInstance($instanceData['model'], $instanceData['foreign_key']);

		// get the count of users appointed as approvers for a given stage
		$countApprovers = $InstanceClass->countApprovers($data['WorkflowInstanceRequest']['wf_stage_id']);
		if ($approvalMethod == WorkflowStage::METHOD_ALL && $approvalsCount >= $countApprovers) {
			return true;
		}

		return false;
	}

	/**
	 * Handles completion process for a request. Sets its status as OK and triggers after completed.
	 * 
	 * @param  int $id     WorkflowInstanceRequest ID.
	 * @return bool True   if process went without issues - request is not yet completed
	 *                     or is already completed or was just now completed.
	 *                     False if issue occured and we couldnt validate/process.
	 */
	public function processRequestCompletion($id) {
		$ret = true;

		if ($this->validateRequestCompletion($id) === false) {
			return true;
		}

		$data = $this->find('first', [
			'conditions' => [
				'WorkflowInstanceRequest.id' => $id
			],
			'fields' => [
				'WorkflowInstanceRequest.wf_instance_id',
				'WorkflowInstanceRequest.wf_stage_id'
			],
			'recursive' => -1
		]);

		$instanceId = $data['WorkflowInstanceRequest']['wf_instance_id'];
		$stageId = $data['WorkflowInstanceRequest']['wf_stage_id'];

		$ret &= $this->setStatus($id, self::STATUS_OK);
		$ret &= $this->WorkflowInstance->switchStage($instanceId, $stageId);
		$ret &= $this->requestCompletionNotify($id);

		$this->logCompletion($id);

		return $ret;
	}

	// log complted request
	public function logCompletion($id) {
		$data = $this->getRequestData($id);

		return $this->WorkflowInstance->WorkflowInstanceLog->add(
			$data['WorkflowInstanceRequest']['wf_instance_id'],
			WorkflowInstanceLog::TYPE_REQUEST_APPROVED,
			sprintf(
				__('Request to change stage to %s has been approved'),
				$data['WorkflowStage']['name']
			)
		);
	}

	public function requestCompletionNotify($id) {
		$data = $this->getRequestData($id);

		// we need a model alias to check the user accesses
		$model = $data['WorkflowInstance']['model'];

		// read the user IDs
		$userIds = $this->getCompletedRequestNotificationUsers($model, $data['WorkflowStage']['id']);
	
		$userEmails = $this->User->getEmails($userIds);
		$email = (new WorkflowsModule())->email([
			'to' => $userEmails,
			'subject' => __('Stage "%s" has been approved', $data['WorkflowStage']['name']),
			'template' => 'approve_stage',
			'viewVars' => [
				'data' => $data
			]
		]);

		return $email->send();
	}

	/**
	 * Get user IDs that are notified when a call request on a stage has been approved.
	 *
	 * @param  string $model 		 Model name to read the data from.
	 * @param  int    $stageId       Stage ID.
	 * @return array                 User IDs
	 */
	public function getCompletedRequestNotificationUsers($model, $stageId) {
		// get the users via WorklowAccessObject easily
		$WorkflowAccessObject = ClassRegistry::init('Workflows.WorkflowAccess')->getAccess($model);

		$users = $WorkflowAccessObject->get(
			['WorkflowStage', $stageId],
			WorkflowAccess::ACCESS_OWNER
		);

		return $users;
	}

	/**
	 * General method to get WorkflowInstanceRequest data for use in notification emails or logs.
	 * 
	 * @param  int   $id Workflow Instance Request ID.
	 * @return array     Data array.
	 */
	public function getRequestData($id) {
		return $this->find('first', [
			'conditions' => [
				'WorkflowInstanceRequest.id' => $id
			],
			'contain' => [
				'WorkflowStageStep',
				'WorkflowStage',
				'WorkflowInstance' => ['model'],
				'User'
			]
		]);
	}

	/*
	 * Statuses for instance request.
	 * @access static
	 */
	 public static function statuses($value = null) {
		$options = array(
			self::STATUS_OK => __('Ok'),
			self::STATUS_PENDING => __('Pending')
		);
		return parent::enum($value, $options);
	}
	const STATUS_PENDING = 2;
	const STATUS_OK = 1;

}