<?php
App::uses('AppModel', 'Model');

class Update extends AppModel
{
	public $useTable = false;

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $actsAs = [
	];

	public $validate = [
	];

	public $hasMany = [
	];

	public $hasAndBelongsToMany = [
	];

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('Updates');
		
		parent::__construct($id, $table, $ds);
	}
}
