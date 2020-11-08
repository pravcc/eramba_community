<?php
App::uses('AppModel', 'Model');

class SecurityServiceMaintenanceDate extends AppModel
{
	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $actsAs = [
		'Containable'
	];

	public $belongsTo = [
		'SecurityService'
	];

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('Security Service Maitenance Dates');
		$this->_group = parent::SECTION_GROUP_CONTROL_CATALOGUE;

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			)
		);

		$this->fieldData = array(
			'day' => array(
				'type' => 'select',
				'editable' => true,
				'label' => __('Day'),
				'options' => [$this, 'dayOptions'],
				'default' => date('j')
			),
			'month' => array(
				'type' => 'select',
				'editable' => true,
				'label' => __('Month'),
				'options' => [$this, 'monthOptions'],
				'default' => date('n')
			)
		);

		parent::__construct($id, $table, $ds);
	}

	// list of day options
	public function dayOptions()
	{
		$options = [];
		for ($i=1; $i < 32; $i++) { 
			$options[$i] = $i;
		}

		return $options;
	}

	// list of day options
	public function monthOptions()
	{
		$options = [
			1 => __('January'),
			2 => __('February'),
			3 => __('March'),
			4 => __('April'),
			5 => __('May'),
			6 => __('June'),
			7 => __('July'),
			8 => __('August'),
			9 => __('September'),
			10 => __('October'),
			11 => __('November'),
			12 => __('December'),
		];
	
		return $options;
	}
}