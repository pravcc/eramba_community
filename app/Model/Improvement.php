<?php
App::uses('AppModel', 'Model');

class Improvement extends AppModel
{
	protected $auditModel = false;
	protected $auditParentModel = false;

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $actsAs = array(
		'Containable',
		'EventManager.EventManager',
	);

	public $validate = array(
		'Project' => array(
			'rule' => array( 'multiple', array( 'min' => 1 ) )
		)
	);

	public $belongsTo = array(
		'User'
	);

	public $hasAndBelongsToMany = array(
		'Project',
		'SecurityIncident'
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Improvements');

		parent::__construct($id, $table, $ds);
	}

	public function beforeDelete($cascade = true) {
		if (!empty($this->id)) {
			$audit = $this->getAudit($this->id);
			$this->planId = $audit[$this->auditModel][$this->{$this->auditModel}->belongsTo[$this->auditParentModel]['foreignKey']];
		}

		return true;
	}

	public function afterDelete() {
		return true;
	}

	public function beforeSave($options = array()) {
		return true;
	}

	public function afterSave($created, $options = array()) {
		return true;
	}

	private function getAudit($id) {
		$data = $this->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'fields' => array($this->belongsTo[$this->auditModel]['foreignKey']),
			'recursive' => -1
		));

		$audit = $this->{$this->auditModel}->find('first', array(
			'conditions' => array(
				'id' => $data[$this->alias][$this->belongsTo[$this->auditModel]['foreignKey']]
			),
			'recursive' => -1
		));

		return $audit;
	}

	protected function getAuditParent() {
		return $this->{$this->auditModel}->{$this->auditParentModel};
	}

	public function getProjects() {
		$data = $this->Project->find('list', [
			'conditions' => [
				'Project.project_status_id !=' => PROJECT_STATUS_COMPLETED
			],
			'order' => ['Project.title' => 'ASC']
		]);

		return $data;
	}

}
