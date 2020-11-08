<?php
App::uses('ObjectVersionAppModel', 'ObjectVersion.Model');
App::uses('AuditLog', 'ObjectVersion.Model');

class Audit extends ObjectVersionAppModel {
	const EVENT_CREATE = 'CREATE';
	const EVENT_EDIT = 'EDIT';
	const EVENT_DELETE = 'DELETE';
	const EVENT_RESTORE = 'RESTORE';

	public $plugin = null;

	public $actsAs = array(
		'Containable',
	);

	public $belongsTo = array(
		'Restore' => array(
			'className' => 'ObjectVersion.Audit',
			'foreignKey' => 'restore_id'
		)
	);

	public $hasMany = array(
		'AuditDelta' => array(
			'className' => 'ObjectVersion.AuditDelta'
		)
	);

	public function beforeSave($options = array()) {
		if (empty($this->id)) {
			$this->data['Audit']['version'] = $this->getVersionNumber($this->data['Audit']['model'], $this->data['Audit']['entity_id']);
		}

		return true;
	}

	protected function getVersionNumber($model, $id) {
		$v = $this->find('first', array(
			'conditions' => array(
				'Audit.model' => $model,
				'Audit.entity_id' => $id
			),
			'fields' => array('MAX(Audit.version) as max_version'),
			'recursive' => -1
		));

		return $v[0]['max_version']+1;
	}

	public function getHistory($model, $foreignKey) {
		$data = $this->find('all', array(
			'conditions' => array(
				'Audit.model' => $model,
				'Audit.entity_id' => $foreignKey
			),
			'order' => array(
				'Audit.created' => 'DESC'
			),
			'contain' => array(
				'AuditDelta',
				'Restore' => array(
					'fields' => array('version')
				)
			)
		));

		return $data;
	}

	public function hasRevision($model) {
		$data = $this->find('list', array(
			'conditions' => array(
				'Audit.model' => $model
			),
			'fields' => ['entity_id'],
			'group' => ['entity_id'],
			'recursive' => -1
		));

		return $data;
	}
}
