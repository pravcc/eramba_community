<?php
/**
 * @package       Workflows.Model
 */

App::uses('CakeEvent', 'Event');
App::uses('CakeEventListener', 'Event');
App::uses('CakeEventManager', 'Event');
App::uses('WorkflowsAppModel', 'Workflows.Model');
App::uses('WorkflowInstanceRequest', 'Workflows.Model');
App::uses('WorkflowInstanceLog', 'Workflows.Model');
App::uses('WorkflowStageStep', 'Workflows.Model');
App::uses('WorkflowSetting', 'Workflows.Model');
App::uses('WorkflowInstanceObject', 'Workflows.Lib');
App::uses('ErambaCakeEmail', 'Network/Email');
App::uses('ClassRegistry', 'Utility');
App::uses('AppModule', 'Lib');

class WorkflowInstance extends WorkflowsAppModel {
	public $useTable = 'wf_instances';

	/**
	 * Holds validation errors that occured during a request from other models.
	 * 
	 * @var array
	 */
	public $requestErrors = [];

	public $actsAs = array(
		'Containable',
		'AuditLog.Auditable'
	);

	public $belongsTo = array(
		'WorkflowStage' => [
			'className' => 'Workflows.WorkflowStage',
			'foreignKey' => 'wf_stage_id'
		],
		'WorkflowSetting' => [
			'className' => 'Workflows.WorkflowSetting',
			'foreignKey' => false,
			'conditions' => [
				'WorkflowSetting.model = WorkflowInstance.model'
			]
		]
	);

	public $hasMany = array(
		'WorkflowInstanceLog' => [
			'className' => 'Workflows.WorkflowInstanceLog',
			'foreignKey' => 'wf_instance_id'
		],
		'WorkflowInstanceRequest' => [
			'className' => 'Workflows.WorkflowInstanceRequest',
			'foreignKey' => 'wf_instance_id'
		],
	);

	public $hasOne = array(
		'PendingRequest' => [
			'className' => 'Workflows.WorkflowInstanceRequest',
			'foreignKey' => 'wf_instance_id',
			'conditions' => [
				'PendingRequest.status' => WorkflowInstanceRequest::STATUS_PENDING
			]
		],
	);

	public $validate = array(
		// 'wf_setting_id' => array(
		// 	'notBlank' => array(
		// 		'rule' => 'notBlank',
		// 		'required' => true,
		// 		'message' => 'This field is required'
		// 	)
		// ),
		'model' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'foreign_key' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			)
		),
		'wf_stage_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			)
		),
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Workflow Instances');

		$this->fieldData = array(
			'wf_stage_id' => array(
				'label' => __('Stage'),
				'editable' => true,
				'options' => [
					'callable' => [$this, 'getNextStageOptions'],
					'passParams' => true
				],
				'empty' => __('Choose a Stage ...')
			),
		);

		parent::__construct($id, $table, $ds);
	}

	public function implementedEvents() {
		return am(parent::implementedEvents(), [
			'WorkflowInstance.afterSwitchStage' => array('callable' => 'afterSwitchStage', 'passParams' => true),
			'Model.WorkflowInstance.afterInstanceChange' => array('callable' => 'afterInstanceChange', 'passParams' => true),
		]);
	}

	protected function triggerChangeEvent($id) {
		$event = new CakeEvent('Model.WorkflowInstance.afterInstanceChange', $this, array($id));
		list($event->break, $event->breakOn) = array(true, array(false, null));
		$this->getEventManager()->dispatch($event);
		if (!$event->result) {
			return false;
		}
	}

	public function afterAuditProperty($Model, $propertyName, $oldValue, $newValue) {
		// after switching a stage we dispatch an event
		if ($propertyName == 'wf_stage_id' && $oldValue != $newValue) {
			$event = new CakeEvent('WorkflowInstance.afterSwitchStage', $this, array($oldValue, $newValue));
			list($event->break, $event->breakOn) = array(true, array(false, null));
			$this->getEventManager()->dispatch($event);
			if (!$event->result) {
				return false;
			}
		}
	}

	/**
	 * Options for next stages select box, queried by model.
	 */
	public function getNextStageOptions(FieldDataEntity $Field, $model) {
		$query = $Field->buildRelatedQuery();
		$query['conditions']['WorkflowStage.model'] = $model;
		
		return $Field->findRelated($query);
	}

	/**
	 * Callback after switching a stage (i.e approval of a called stage is succesful or force a stage).
	 * 
	 * @param  int $id      Workflow Instance ID.
	 * @return bool         True if ok, false to stop execution.
	 */
	public function afterSwitchStage($oldStageId, $newStageId) {
		$ret = true;

		$id = $this->id;

		$stage = $this->WorkflowStage->getItem($oldStageId);
		$oldStageName = $stage['WorkflowStage']['name'];

		$stage = $this->WorkflowStage->getItem($newStageId);
		$newStageName = $stage['WorkflowStage']['name'];

		$ret &= $this->WorkflowInstanceLog->add(
			$id,
			WorkflowInstanceLog::TYPE_SWITCH_STAGE,
			sprintf(
				__('Stage has been changed from %s to %s'),
				$oldStageName,
				$newStageName
			)
		);

		return $ret;
	}

	/**
	 * Triggers after a change in a workflow instance.
	 */
	public function afterInstanceChange($id) {
		Cache::clearGroup('WorkflowsModule', 'workflows_instances');
	}

	/*
	 * Statuses for the current instance of an object.
	 * @access static
	 */
	 public static function statuses($value = null) {
		$options = array(
			self::STATUS_OK => __('Ok'),
			// self::STATUS_REQUEST_PENDING => __('Pending Request')
		);
		return parent::enum($value, $options);
	}
	const STATUS_OK = 1;

	public function getInstance($model, $foreignKey, $getAccess = true) {
		$data = $this->getItem($model, $foreignKey);
		$WorkflowInstanceObject = new WorkflowInstanceObject($data, $model);

		// if needed we get access object also to check rights
		if ($getAccess) {
			$WorkflowAccessObject = ClassRegistry::init('Workflows.WorkflowAccess')->getAccess($model);
			$WorkflowInstanceObject->setInstanceAccess($WorkflowAccessObject);
		}

		return $WorkflowInstanceObject;
	}

	public function getObjectTitle(WorkflowInstanceObject $Instance) {

	}

	/**
	 * Cron method checks the timeout expiration of instances that are currently on a stage that has rollback configured.
	 * 
	 * @return boolean  True on success, False otherwise.
	 */
	public function cron() {
		$data = $this->find('all', [
			'conditions' => [
				// check only instances without pending request
				'WorkflowInstance.pending_requests' => 0,

				// check only instances that exist on enabled workflow configuration
				'WorkflowSetting.status' => WorkflowSetting::STATUS_ENABLED,

				// check only instances that are currently on a stage that have a rollback step configured
				'WorkflowStageStep.step_type' => WorkflowStageStep::STEP_TYPE_ROLLBACK
			],
			'joins' => [
				[
					'table' => 'wf_settings',
					'alias' => 'WorkflowSetting',
					'type' => 'INNER',
					'conditions' => $this->belongsTo['WorkflowSetting']['conditions']
				],
				[
					'table' => 'wf_stage_steps',
					'alias' => 'WorkflowStageStep',
					'type' => 'INNER',
					'conditions' => [
						'WorkflowStageStep.wf_stage_id = WorkflowInstance.wf_stage_id'
					]
				]
			],
			'group' => ['WorkflowInstance.id'],
			'fields' => [
				'WorkflowInstance.id',
				'WorkflowInstance.model',
				'WorkflowInstance.foreign_key',
				'WorkflowStageStep.id',
				'WorkflowStageStep.wf_next_stage_id'
			],
			'recursive' => -1
		]);
		
		$ret = true;
		foreach ($data as $instance) {
			$InstanceClass = $this->getInstance(
				$instance['WorkflowInstance']['model'],
				$instance['WorkflowInstance']['foreign_key'],
				false
			);

			// we want to process only instances without pending request and with a rollback step only
			// this is second check, find() above should handle that properly
			if ($InstanceClass->isStatusPending() || !$InstanceClass->hasRollback()) {
				continue;
			}

			// means stage expired and calls a rollback step
			if ($InstanceClass->stageExpires() <= 0) {
				$ret &= $this->call_stage(
					$instance['WorkflowInstance']['id'],
					$instance['WorkflowStageStep']['wf_next_stage_id'],
					$instance['WorkflowStageStep']['id']
				);
			}
		}

		return $ret;
	}

	/**
	 * Checks if a workflow instance record exists and auto-creates it if missing, only if the section is whitelisted.
	 * 
	 * @param  Model $Model        Model Instance for which to retrieve the setting record.
	 * @return mixed               Array of data on success, False on failure in case given section is not whitelisted.
	 */
	public function getItem($model, $foreignKey) {
		if (!$this->WorkflowSetting->isEnabled($model)) {
			return false;
		}

		// Autocreate setting record in case its still missing as its required to proceed
		$Setting = $this->WorkflowStage->WorkflowSetting->getItem($model);

		if ($model instanceof AppModel) {
			$model = $model->alias;
		}

		if (!$this->autoCreate($model, $foreignKey)) {
			trigger_error(__('WorkflowInstance record failed to auto-create and is required to operate workflows.'));
		}

		$this->bindObject($model);

		$cacheStr = '_instance_'. $model . '_' . $foreignKey;
		if (($data = Cache::read($cacheStr, 'workflows_instances')) === false) {
			$this->Object->virtualFields = [
				'object_model_label' => '"' . $this->Object->label(['singular' => true]) . '"',
				'object_item_label' => 'Object.' . $this->Object->displayField
			];

			$data = $this->find('first', array(
				'conditions' => array(
					$this->alias . '.model' => $model,
					$this->alias . '.foreign_key' => $foreignKey
				),
				'contain' => [
					'Object',
					'WorkflowSetting' => [
						'fields' => ['id', 'name']
					],
					'WorkflowStage' => [
					// 'WorkflowStageStep' => ['WorkflowStageStepCondition'],
						'NextStage' ,
						'DefaultStep' => [
							'WorkflowNextStage'
						],
						'RollbackStep' => [
							'WorkflowNextStage'
						]
					],
					'PendingRequest' => [
						'UserApproval',
						'WorkflowStage' => [
							'fields' => ['id', 'name']
						]
					]
				]
			));

			unset($this->Object->virtualFields['object_model_label']);
			unset($this->Object->virtualFields['object_item_label']);

			if (empty($data)) {
				throw new NotFoundException();
			}

			Cache::write($cacheStr, $data, 'workflows_instances');
		}

		return $data;
	}

	/**
	 * Binds object model to the WorkflowInstance model for data accessibility.
	 */
	public function bindObject($model) {
		if ($this->getAssociated('Object') !== null) {
			return true;
		}

		return $this->bindModel([
			'belongsTo' => [
				'Object' => [
					'className' => $model,
					'foreignKey' => 'foreign_key'
				]
			]
		], false);
	}

	/**
	 * Auto-creates a new instance record if there is not any already.
	 * 
	 * @param  string     $model  Model.
	 * @return bool|array         False on failed save of the setting, array of new saved data on success.
	 */
	public function autoCreate($model, $foreignKey = null) {
		if (!in_array($model, AppModule::instance('Workflows')->whitelist())) {
			throw new ForbiddenException();
		}

		if (!$this->WorkflowSetting->isEnabled($model)) {
			return true;
		}

		// manage all objects for $model in case no $foreignKey was specified
		if ($foreignKey === null) {
			$this->bindObject($model);
			$listAdd = $this->Object->find('list', [
				'conditions' => [
					'id !=' => $this->findList($model)
				],
				'fields' => ['id', 'id'],
				'recursive' => -1
			]);

			$ret = true;
			foreach ($listAdd as $id) {
				$ret &= $this->autoCreate($model, $id);
			}

			return $ret;
		}

		if ($this->itemExists($model, $foreignKey)) {
			return true;
		}

		// Lets find the initial stage assigned for the current section,
		// as instance of an object begins the workflow on that stage. 
		$initialStage = $this->WorkflowStage->getInitialStage($model);
		$initialStageId = $initialStage['WorkflowStage']['id'];

		$this->create();
		$this->set([
			'model' => $model,
			'foreign_key' => $foreignKey,
			'wf_stage_id' => $initialStageId,
			'status' => self::STATUS_OK
		]);

		$ret = $this->save();

		// we trigger also switch stage method to reload required data
		$ret &= $this->switchStage($this->id, $initialStageId);

		return $ret;
	}

	/**
	 * Check if a setting row exists in database.
	 * 
	 * @param  string  $model Model.
	 * @return bool           True if exists, false otherwise.
	 */
	public function itemExists($model, $foreignKey) {
		return (bool)$this->find('count', array(
			'conditions' => array(
				$this->alias . '.model' => $model,
				$this->alias . '.foreign_key' => $foreignKey
			),
			'recursive' => -1
		));
	}

	public function findList($model) {
		return $this->find('list', array(
			'conditions' => array(
				$this->alias . '.model' => $model
			),
			'fields' => ['id', 'foreign_key'],
			'recursive' => -1
		));
	}

	/**
	 * Get Instances on $model that doesnt have any pending requests.
	 */
	public function getNoRequestInstances($model) {
		$data = $this->find('list', [
			'conditions' => array(
				$this->alias . '.model' => $model,
				$this->alias . '.pending_requests' => 0
			),
			'fields' => ['id', 'id'],
			'recursive' => -1
		]);

		return $data;
	}

	/**
	 * Swithces a stage on a specified Workflow Instance.
	 */
	public function switchStage($id, $stageId) {
		$this->id = $id;
		$saveData = [
			'wf_stage_id' => $stageId,
			'stage_init_date' => CakeTime::format(CakeTime::fromString('now'), '%Y-%m-%d %H:%M:%S')
		];

		$this->set($saveData);

		$ret = true;

		$ret &= $this->save(null, [
			'fieldList' => array_keys($saveData)
		]);

		// switching stage automatically changes the status of this instance to OK as default.
		// $ret &= $this->setStatus($this->id, self::STATUS_OK);
		
		// and also we check if there is not any stuck pending request for this instance, and reset it
		if ($this->WorkflowInstanceRequest->hasPending($id)) {
			$ret &= $this->WorkflowInstanceRequest->updateAll([
				'WorkflowInstanceRequest.status' => WorkflowInstanceRequest::STATUS_OK
			], [
				'WorkflowInstanceRequest.wf_instance_id' => $id,
				'WorkflowInstanceRequest.status' => WorkflowInstanceRequest::STATUS_PENDING
			]);
		}

		if ($ret) {
			$this->triggerChangeEvent($id);
		}

		return $ret;
	}

	/**
	 * Wrapper method to process a single request and stores validation errors from that model
	 * for further use in controller.
	 * 
	 * @param  array $callback  Callback.
	 * @param  array $args      Arguments for the callback.
	 * @return bool             True on success, False otherwise.
	 */
	protected function _processRequest($callback, $args) {
		$ret = true;

		$ret &= call_user_func_array($callback, $args);
		$this->requestErrors = $callback[0]->validationErrors;

		if ($ret) {
			$this->triggerChangeEvent($args[0]);
		}

		return $ret;
	}

	/**
	 * Calls a stage on an instance.
	 * 
	 * @param int $instanceId  		Instance ID.
	 * @param int $stageId     		Stage ID that is being requested.
	 * @param null|int $stageStepId When not null then it means a conditional step was triggered,
	 *                              otherwise user triggered a call on a default stage.
	 *                              @see  Workflows.TriggerableBehavior
	 */
	public function call_stage($id, $stageId, $stageStepId = null) {
		// in case $stageStepId is null value, we pull a default step, because its possible only that one is it
		// now its possible to have only 1 stage called and 1 default step at the same time
		if ($stageStepId === null) {
			$stageStep = $this->WorkflowStage->WorkflowStageStep->getNextDefaultStep($stageId);
			if (empty($stageStep)) {
				throw new NotFoundException();
			}

			$stageStepId = $stageStep['WorkflowStageStep']['id'];
		}
		
		return $this->_processRequest(
			[$this->WorkflowInstanceRequest, 'addRequest'],
			[$id, $stageId, $stageStepId]
		);
	}

	// approve a stage general method
	public function approve_stage($id, $stageId) {
		return $this->_processRequest(
			[$this->WorkflowInstanceRequest->WorkflowInstanceApproval, 'addApproval'],
			[$id, $stageId]
		);
	}

	// request for a force stage
	public function force_stage($id, $stageId) {
		return $this->_processRequest(
			[$this, 'switchStage'],
			[$id, $stageId]
		);
	}

	// /**
	//  * Sends out notification emails to the users that has access to this feature.
	//  */
	// protected function _callStageNotifications($stagesJoinId) {
	// 	$notifyUsers = $this->WorkflowStage->WorkflowStageStep->WorkflowStageStepsManager->getManagers(
	// 		WorkflowStageStepsManager::MANAGE_NOTIFY,
	// 		$stagesJoinId
	// 	);

	// 	$userIds = $notifyUsers->getUsers();
	// 	$emails = $this->WorkflowStage->WorkflowStageStep->CallUser->getEmails($userIds);

			
	// 	$email->to($emails);
	// 	$email->subject('test call stage');
	// 	$email->template('test');

	// 	return $email->send();
	// }

}