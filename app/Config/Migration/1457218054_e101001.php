<?php
App::uses('AppMigration', 'Lib');
class E101001 extends AppMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'e1.0.1.001';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		parent::before($direction);

		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		parent::after($direction);

		$ret = true;
		if ($direction == 'up') {
			$ret &= $this->integrityPrecheck();

			try {
				$ret &= $this->addForeignKey(array('compliance_audit_auditee_feedbacks', 'user_id'), array('users', 'id'));

				$ret &= $this->addForeignKey(array('compliance_audit_auditee_feedbacks', 'compliance_audit_setting_id'), array('compliance_audit_settings', 'id'));

				$ret &= $this->addForeignKey(
					array('compliance_audit_auditee_feedbacks', 'compliance_audit_feedback_profile_id'),
					array('compliance_audit_feedback_profiles', 'id'),
					null,
					'compliance_audit_auditee_feedbacks_ibfk_3'
				);

				$ret &= $this->addForeignKey(array('compliance_audit_auditee_feedbacks', 'compliance_audit_feedback_id'), array('compliance_audit_feedbacks', 'id'));
			}
			catch (Exception $e) {

			}

			if (!$ret) {
				return false;
			}
			
			// add updates to settings
			$ret &= $this->save('SettingGroup', array(
				'slug' => 'UPDATES',
				'parent_slug' => 'SEC',
				'name' => 'Updates',
				'url' => '{"controller":"updates","action":"index"}'
			), false, true);

			// re-enable backup and restore in settings functionality
			$SettingGroup = $this->initModel('SettingGroup');
			$ds = $SettingGroup->getDataSource();
			$hidden = $ds->value(0, 'string');

			$ret &= $SettingGroup->updateAll(
				array(
					'SettingGroup.hidden' => $hidden
				),
				array('SettingGroup.slug' => 'BAR')
			);
		}

		return $ret;
	}

	private function integrityPrecheck() {
		$Auditee = $this->initModel('ComplianceAuditAuditeeFeedback');
		$User = $this->initModel('User');

		$data = $Auditee->find('all', array(
			'fields' => array(
				'ComplianceAuditAuditeeFeedback.id',
				'ComplianceAuditAuditeeFeedback.user_id',
				'User.id',
				'ComplianceAuditSetting.id',
				'ComplianceAuditFeedbackProfile.id',
				'ComplianceAuditFeedback.id'
			),
			'recursive' => 0
		));

		$ret = true;
		foreach ($data as $item) {
			if (empty($item['User']['id'])) {
				$User->id = $item['ComplianceAuditAuditeeFeedback']['user_id'];
				$ret &= $User->makeFieldAdmin('ComplianceAuditAuditeeFeedback', 'user_id');
			}

			if (empty($item['ComplianceAuditSetting']['id']) || empty($item['ComplianceAuditFeedbackProfile']['id']) || empty($item['ComplianceAuditFeedback']['id'])) {
				$ret &= $Auditee->delete($item['ComplianceAuditAuditeeFeedback']['id']);
			}
		}

		return $ret;
	}
}
