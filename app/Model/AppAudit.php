<?php
// class shares the same status functionality if extended
App::uses('AppModel', 'Model');

class AppAudit extends AppModel
{
	protected $auditParentModel = false;

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	/*
     * static enum: Model::function()
     * @access static
     */
    public static function results($value = null) {
        $options = array(
            self::RESULT_FAILED => __('Fail'),
            self::RESULT_PASSED => __('Pass'),
        );
        return parent::enum($value, $options);
    }

    const RESULT_FAILED = AUDIT_FAILED;
    const RESULT_PASSED = AUDIT_PASSED;

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->auditParentForeignKey = $this->belongsTo[$this->auditParentModel]['foreignKey'];
	}

	// getter for parent ID that is compatible with newly migrated audits that uses visualisation and also previous audits
	public function getAuditParentId() {
		if ($this instanceof InheritanceInterface) {
			return $this->parentNodeId();
		}

		$audit = $this->getAudit();

		// compatibility for previous versions
		return (!empty($audit)) ? $audit[$this->alias][$this->auditParentForeignKey] : false;
	}

	public function beforeDelete($cascade = true) {
		$ret = true;
		if (!empty($this->id)) {
			$audit = $this->getAudit();
			if (empty($audit)) {
				return true;
			}
			
			$this->parentId = $this->getAuditParentId();
		}

		return $ret;
	}

	public function afterDelete() {
		$ret = true;

		return $ret;
	}

	public function beforeSave($options = array()) {
		$ret = true;

		$parentId = $this->getAuditParentId();
		if ($parentId !== false) {
			$ret &= $this->{$this->auditParentModel}->saveAudits($parentId, 'before');
		}

		return $ret;
	}

	public function afterSave($created, $options = array()) {
		$ret = true;

		$parentId = $this->getAuditParentId();
		$ret &= $this->{$this->auditParentModel}->saveAudits($parentId, 'after');
		
		return $ret;
	}

	private function getAudit() {
		$audit = $this->find('first', array(
			'conditions' => array(
				'id' => $this->id
			),
			'recursive' => -1
		));

		return $audit;
	}

}
