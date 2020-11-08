<?php
App::uses('SystemHealthLib', 'Lib');

class SystemHealthLibTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
	}

/**
 * test
 *
 * @return void
 */
	public function testMysql() {
		$SystemHealthLib = new SystemHealthLib();

		$testVersion1 = '10.1.26-MariaDB-0+deb9u1';
		$testVersion2 = '10.1.21-MariaDB';
		$testVersion3 = '5.5.5-10.1.21-MariaDB';
		$testVersionFail1 = '5.5.5-10.0.14-MariaDB';

		$this->assertTrue($SystemHealthLib->mysql($testVersion1));
		$this->assertTrue($SystemHealthLib->mysql($testVersion2));
		$this->assertTrue($SystemHealthLib->mysql($testVersion3));
		$this->assertFalse($SystemHealthLib->mysql($testVersionFail1));

	}
}
