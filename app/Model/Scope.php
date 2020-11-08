<?php
App::uses('AppModel', 'Model');

class Scope extends AppModel
{
	public $displayField = 'id';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $actsAs = array(
		'Containable'
	);

	public $validate = array(
	);

	public $belongsTo = array(
		'CisoRole' => array(
			'className' => 'User',
			'foreignKey' => 'ciso_role_id',
			'fields' => array('id', 'name', 'surname')
		),
		'CisoDeputy' => array(
			'className' => 'User',
			'foreignKey' => 'ciso_deputy_id',
			'fields' => array('id', 'name', 'surname')
		),
		'BoardRepresentative' => array(
			'className' => 'User',
			'foreignKey' => 'board_representative_id',
			'fields' => array('id', 'name', 'surname')
		),
		'BoardRepresentativeDeputy' => array(
			'className' => 'User',
			'foreignKey' => 'board_representative_deputy_id',
			'fields' => array('id', 'name', 'surname')
		),
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('System Role');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'ciso_role_id' => [
				'label' => __('CISO Role'),
				'editable' => false,
			],
			'ciso_deputy_id' => [
				'label' => __('CISO Deputy'),
				'editable' => false,
			],
			'board_representative_id' => [
				'label' => __('Board Representative'),
				'editable' => false,
			],
			'board_representative_deputy_id' => [
				'label' => __('Board Representative Deputy'),
				'editable' => false,
			],
		];

		parent::__construct($id, $table, $ds);
	}

}
