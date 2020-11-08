<?php
/**
 * @package       Workflows.Model
 */

App::uses('WorkflowsAppModel', 'Workflows.Model');

class WorkflowInstanceLog extends WorkflowsAppModel {
	public $useTable = 'wf_instance_logs';

	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = array(
		'WorkflowInstance' => [
			'className' => 'Workflows.WorkflowInstance',
			'foreignKey' => 'wf_instance_id'
		],
		'User'
	);

	public $validate = [
		'wf_instance_id' => [
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			)
		],
		'user_id' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			]
		],
		'type' => [
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field is required'
			)
		]
	];

	// adds a new request to specified instance
	public function add($instanceId, $type, $message = null) {
		$currentUser = $this->currentUser('id');

		$this->create();
		$this->set([
			'wf_instance_id' => $instanceId,
			'type' => $type,
			'message' => $message,
			'user_id' => $currentUser
		]);

		return $this->save();
	}

	/*
	 * Types for the logs.
	 * @access static
	 */
	 public static function types($value = null) {
		$options = array(
			self::TYPE_ADD_REQUEST => __('New Request'),
			self::TYPE_ADD_APPROVAL => __('New Approval'),
			self::TYPE_REQUEST_APPROVED => __('Request Approved'),
			self::TYPE_SWITCH_STAGE => __('Stage Changed')
		);
		return parent::enum($value, $options);
	}
	const TYPE_ADD_REQUEST = 1;
	const TYPE_ADD_APPROVAL = 2;
	const TYPE_REQUEST_APPROVED = 3;
	const TYPE_SWITCH_STAGE = 4;

}