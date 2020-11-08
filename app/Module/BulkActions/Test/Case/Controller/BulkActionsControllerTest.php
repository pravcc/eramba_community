<?php
App::uses('BulkActionsController', 'BulkActions.Controller');

/**
 * BulkActionsController Test Case
 */
class BulkActionsControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.bulk_actions.bulk_action',
		'plugin.bulk_actions.setting',
		'plugin.bulk_actions.setting_group',
		'plugin.bulk_actions.notification',
		'plugin.bulk_actions.user',
		'plugin.bulk_actions.group',
		'plugin.bulk_actions.attachment',
		'plugin.bulk_actions.comment',
		'plugin.bulk_actions.system_record',
		'plugin.bulk_actions.workflows_validator',
		'plugin.bulk_actions.workflow',
		'plugin.bulk_actions.workflows_custom_validator',
		'plugin.bulk_actions.workflows_custom_approver',
		'plugin.bulk_actions.workflows_all_validator_item',
		'plugin.bulk_actions.workflows_all_approver_item',
		'plugin.bulk_actions.workflows_approver',
		'plugin.bulk_actions.workflows_validator_scope',
		'plugin.bulk_actions.workflows_approver_scope',
		'plugin.bulk_actions.user_ban'
	);

}
