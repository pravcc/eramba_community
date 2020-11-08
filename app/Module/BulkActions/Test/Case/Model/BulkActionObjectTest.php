<?php
App::uses('BulkActionObject', 'BulkActions.Model');

/**
 * BulkActionObject Test Case
 */
class BulkActionObjectTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.bulk_actions.bulk_action_object',
		'plugin.bulk_actions.bulk_action'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->BulkActionObject = ClassRegistry::init('BulkActions.BulkActionObject');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->BulkActionObject);

		parent::tearDown();
	}

}
