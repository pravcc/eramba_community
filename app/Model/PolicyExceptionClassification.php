<?php
App::uses('AppModel', 'Model');

class PolicyExceptionClassification extends AppModel
{
	public $displayField = 'name';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $actsAs = [];

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('Policy Exception Classifications');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
            'name' => [
                'label' => __('Name'),
                'editable' => true
            ],
        ];
		
		parent::__construct($id, $table, $ds);
	}
}
