<?php
App::uses('ImportToolData', 'ImportTool.Lib');
App::uses('Model', 'Model');

class ImportToolTestModel extends Model {
	public $useTable = false;
	public $alias = 'Post';

	public $actsAs = [
		'HtmlPurifier.HtmlPurifier'
	];

	public $importArgs = array(
		'Post.name' => array(
			'name' => 'Name',
			'headerTooltip' => 'todo'
		),
		'Post.description' => array(
			'name' => 'Description',
			'headerTooltip' => 'todo'
		),
		'Post.dropdown_id' => array(
			'name' => 'BelongsTo',
			'model' => 'BelongsTo',
			'headerTooltip' => 'todo'
		),
		'Post.Habtm' => array(
			'name' => 'Habtm',
			'model' => 'Habtm',
			'headerTooltip' => 'todo'
		),
		'Post.test' => array(
			'name' => 'test',
			'model' => 'test'
		)
	);

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->hasAndBelongsToMany['Habtm'] = [
			'className' => 'ImportToolTestModelHabtm'
		];
	}

}

class ImportToolTestModelHabtm extends Model {
	public $useTable = false;
}

class ImportToolDataTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Model = ClassRegistry::init('ImportToolTestModel');
		$this->Model->Habtm = ClassRegistry::init('ImportToolTestModelHabtm');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Model);
	}

/**
 * test
 *
 * @return void
 */
	public function testData() {
		$testData = [
			0 => [0,0,0,0,0],
			1 => [1,1,1,1,1],
			2 => ['', '', '', '', ''],
			3 => ['test', 'test', 'test', 'test', 'test'],
			4 => ['test1|test2', 'test1|test2', 'test1|test2', 'test1|test2', 'test1|test2',]
		];

		$path = App::pluginPath('ImportTool');
		$ImportToolData = new ImportToolData($this->Model, $testData);
		$this->assertEqual($ImportToolData->getArgumentsCount(), 5, 'Arguments does not match');

		$importableData = $ImportToolData->getImportableDataArray();

		// check habtm fields if they explode by separator
		$this->assertEqual($importableData[1]['Post']['Habtm'], [0 => '1']);
		$this->assertEqual($importableData[3]['Post']['Habtm'], [0 => 'test']);
		$this->assertEqual($importableData[4]['Post']['Habtm'], [0 => 'test1', 1 => 'test2']);

		// check zeros as values
		$this->assertEqual($importableData[0]['Post']['name'], '0');
		$this->assertEqual($importableData[0]['Post']['description'], '0');

		// non-simple-text fields removes the zero if its the only value there
		$this->assertEqual($importableData[0]['Post']['dropdown_id'], '');
		$this->assertEqual($importableData[0]['Post']['Habtm'], '');
		$this->assertEqual($importableData[0]['Post']['test'], '');

	}
}
