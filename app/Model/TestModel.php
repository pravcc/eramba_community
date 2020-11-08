<?php
App::uses('TestTrait', 'Model/Trait');

class TestModel extends AppModel {
	use TestTrait;

	public $useTable = 'legals';
}