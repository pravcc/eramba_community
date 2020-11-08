<?php
App::uses('AppMigration', 'Lib');

App::uses('AppController', 'Controller');
App::uses('AclManagerComponent', 'Acl.Controller/Component');
App::uses('Controller', 'Controller');
App::uses('AclAppController', 'Acl.Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('AclComponent', 'Controller/Component');
App::uses('DbAcl', 'Model');
App::uses('CacheDbAcl', 'Lib');
class E101005 extends AppMigration {

/**
 * Should this migration update Database version in `settings` table on current DataSource connection.
 *
 * @var bool
 */
	public $updateVersion = true;

/**
 * Migration description. Used as a database version after successful migration if `$this->updateVersion` is true.
 *
 * @var string
 */
	public $description = 'e1.0.1.005';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'compliance_audits' => array(
					'third_party_contact_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'after' => 'auditor_id'),
					'auditee_title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 155, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'end_date'),
					'auditee_instructions' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'auditee_title'),
					'use_default_template' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1, 'unsigned' => false, 'after' => 'auditee_instructions'),
					'email_subject' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'use_default_template'),
					'email_body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'email_subject'),
					'indexes' => array(
						'third_party_contact_id' => array('column' => 'third_party_contact_id', 'unique' => 0),
					),
				),
				'notification_system_items' => array(
					'automated' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false, 'after' => 'trigger_period'),
					'email_customized' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false, 'after' => 'automated'),
					'email_subject' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'email_customized'),
					'email_body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'email_subject'),
				),
				'risks_security_policies' => array(
					'type' => array('type' => 'string', 'null' => false, 'default' => 'treatment', 'length' => 50, 'collate' => 'utf8_general_ci', 'comment' => '\'treatment\',\'incident\'', 'charset' => 'utf8', 'after' => 'security_policy_id'),
				),
				'security_policies' => array(
					'url' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'description'),
				),
			),
			'create_table' => array(
				'compliance_exceptions_compliance_findings' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'compliance_exception_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'compliance_finding_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'compliance_exception_id' => array('column' => 'compliance_exception_id', 'unique' => 0),
						'compliance_finding_id' => array('column' => 'compliance_finding_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'compliance_findings_third_party_risks' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'compliance_finding_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'third_party_risk_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'compliance_finding_id' => array('column' => 'compliance_finding_id', 'unique' => 0),
						'third_party_risk_id' => array('column' => 'third_party_risk_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'compliance_audits' => array('third_party_contact_id', 'auditee_title', 'auditee_instructions', 'use_default_template', 'email_subject', 'email_body', 'indexes' => array('third_party_contact_id')),
				'notification_system_items' => array('automated', 'email_customized', 'email_subject', 'email_body'),
				'risks_security_policies' => array('type'),
				'security_policies' => array('url'),
			),
			'drop_table' => array(
				'compliance_exceptions_compliance_findings', 'compliance_findings_third_party_risks'
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		$ret = parent::before($direction);

		if ($direction == 'down') {

			$ret &= $this->manageAclDown();
			$ret &= $this->manageDataDown();

			try {
				$ret &= $this->dropForeignKey('compliance_audits', null, 'compliance_audits_ibfk_3');

				$ret &= $this->dropForeignKey(
					'compliance_exceptions_compliance_findings',
					null,
					'compliance_exceptions_compliance_findings_ibfk1'
				);
				$ret &= $this->dropForeignKey(
					'compliance_exceptions_compliance_findings',
					null,
					'compliance_exceptions_compliance_findings_ibfk2'
				);

				$ret &= $this->dropForeignKey(
					'compliance_findings_third_party_risks',
					null,
					'compliance_findings_third_party_risks_ibfk1'
				);
				$ret &= $this->dropForeignKey(
					'compliance_findings_third_party_risks',
					null,
					'compliance_findings_third_party_risks_ibfk2'
				);
			}
			catch (Exception $e) {
				return false;
			}
		}

		return $ret;
	}

	private function manageDataDown() {
		$ret = true;

		$Workflow = $this->generateModel('Workflow');

		$ret &= $Workflow->updateAll(
			array(
				'Workflow.parent_id' => NULL
			),
			array('Workflow.model' => 'SecurityServiceAudit')
		);

		return $ret;
	}

	/**
	 * ACL fixes for Third Party Audits Auditee cancelAction.
	 */
	private function manageAclDown() {
		$collection = new ComponentCollection();
		$this->Acl = new AclComponent($collection);
		$controller = new Controller();
		$AclAppController = new AclAppController();
		$this->Acl->startup($controller);
		$this->AclManager = new AclManagerComponent($collection);
		$this->AclManager->initialize($AclAppController);

		$group = ClassRegistry::init('Group');

		//Third Party Audits role checking
		$auditsRole = $group->find('count', array(
			'conditions' => array(
				'Group.id' => 11
			),
			'recursive' => -1
		));

		if (empty($auditsRole)) {
			return true;
		}

		$ret = true;
		
		$role =& $group;
		$role->id = 11;

		$aco_path = 'ajax/cancelAction';
		$aro_node = $this->Acl->Aro->node($role);
		if(!empty($aro_node)) {
			$ret &= $this->AclManager->save_permission($aro_node, $aco_path, 'deny');
		}

		$aco_path = 'complianceAudits/cancelAction';
		$aro_node = $this->Acl->Aro->node($role);
		if(!empty($aro_node)) {
			$ret &= $this->AclManager->save_permission($aro_node, $aco_path, 'deny');
		}

		return $ret;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		$ret = parent::after($direction);

		if ($direction == 'up') {
			try {
				$ret &= $this->addForeignKey(
					array('compliance_audits', 'third_party_contact_id'),
					array('users', 'id'),
					array('update' => 'CASCADE', 'delete' => 'SET NULL'),
					'compliance_audits_ibfk_3'
				);

				$ret &= $this->addForeignKey(
					array('compliance_exceptions_compliance_findings', 'compliance_exception_id'),
					array('compliance_exceptions', 'id'),
					null,
					'compliance_exceptions_compliance_findings_ibfk1'
				);

				$ret &= $this->addForeignKey(
					array('compliance_exceptions_compliance_findings', 'compliance_finding_id'),
					array('compliance_findings', 'id'),
					null,
					'compliance_exceptions_compliance_findings_ibfk2'
				);

				$ret &= $this->addForeignKey(
					array('compliance_findings_third_party_risks', 'compliance_finding_id'),
					array('compliance_findings', 'id'),
					null,
					'compliance_findings_third_party_risks_ibfk1'
				);

				$ret &= $this->addForeignKey(
					array('compliance_findings_third_party_risks', 'third_party_risk_id'),
					array('third_party_risks', 'id'),
					null,
					'compliance_findings_third_party_risks_ibfk2'
				);
			}
			catch (Exception $e) {
				return false;
			}

			if (!$ret) {
				return false;
			}

			$ret &= $this->manageDataUp();
			$ret &= $this->manageAclUp();
		}
		
		return $ret;
	}

	private function manageDataUp() {
		$ret = true;

		$Workflow = $this->generateModel('Workflow');

		$securityServiceWorkflow = $Workflow->find('first', array(
			'conditions' => array(
				'Workflow.model' => 'SecurityService'
			),
			'fields' => array('id')
		));

		$ret &= $Workflow->updateAll(
			array(
				'Workflow.parent_id' => "'" . $securityServiceWorkflow['Workflow']['id'] . "'"
			),
			array('Workflow.model' => 'SecurityServiceAudit')
		);

		// add updates to settings
		$ret &= $this->save('SettingGroup', array(
			'slug' => 'NOTIFICATION',
			'parent_slug' => 'ACCESSMGT',
			'name' => 'Notifications',
			'url' => '{"controller":"notificationSystem","action":"listItems"}'
		), false, true);

		$RisksSecurityPolicy = $this->generateModel('RisksSecurityPolicy');

		$ds = $RisksSecurityPolicy->getDataSource();
		$incident = $ds->value('incident', 'string');

		$RisksSecurityPolicy->updateAll(
			array(
				'RisksSecurityPolicy.type' => $incident
			),
			array('RisksSecurityPolicy.risk_type' => array('third-party-risk', 'business-risk'))
		);

		return $ret;
	}

	/**
	 * ACL fixes for Third Party Audits Auditee cancelAction.
	 */
	private function manageAclUp() {
		$collection = new ComponentCollection();
		$this->Acl = new AclComponent($collection);
		$controller = new Controller();
		$AclAppController = new AclAppController();
		$this->Acl->startup($controller);
		$this->AclManager = new AclManagerComponent($collection);
		$this->AclManager->initialize($AclAppController);

		$group = ClassRegistry::init('Group');

		//Third Party Audits role checking
		$auditsRole = $group->find('count', array(
			'conditions' => array(
				'Group.id' => 11
			),
			'recursive' => -1
		));

		if (empty($auditsRole)) {
			return true;
		}

		$ret = true;
		
		$role =& $group;
		$role->id = 11;

		$aco_path = 'ajax/cancelAction';
		$aro_node = $this->Acl->Aro->node($role);
		if(!empty($aro_node)) {
			$ret &= $this->AclManager->save_permission($aro_node, $aco_path, 'grant');
		}

		$aco_path = 'complianceAudits/cancelAction';
		$aro_node = $this->Acl->Aro->node($role);
		if(!empty($aro_node)) {
			$ret &= $this->AclManager->save_permission($aro_node, $aco_path, 'grant');
		}

		return $ret;
	}
}
