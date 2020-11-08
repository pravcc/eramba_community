<?php
App::uses('ImportToolCsv', 'ImportTool.Lib');

class ImportToolCsvTest extends CakeTestCase {

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
	public function testCsvFile() {
		// assert
		$path = App::pluginPath('ImportTool');
		$ImportToolCsv = new ImportToolCsv($path . DS . 'Test' . DS . 'tmp' . DS . 'test.csv');
		$this->assertEmpty($ImportToolCsv->getErrors(), 'Errors should be empty');
		$this->assertNotEmpty($ImportToolCsv->getData(), 'Csv file is full of testing data');

	}
}
