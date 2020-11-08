<?php
App::uses('AppModel', 'Model');

class ProgramIssueType extends AppModel
{
	public $displayField = 'type';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $actsAs = [
		'Containable',
		'Search.Searchable',
	];

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('Program Issue Types');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'type' => [
				'label' => __('Type'),
				'editable' => false,
				'hidden' => true,
			],
		];

		parent::__construct($id, $table, $ds);
	}
}
