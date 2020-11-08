<?php
class ServiceClassification extends AppModel {
	public $mapping = array(
		'titleColumn' => 'name',
		'logRecords' => true,
		'workflow' => false
	);
	
	public $actsAs = array(
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'description'
			)
		)
	);

	public $validate = array(
		'name' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		)
	);

	public $hasMany = array(
		'SecurityService'
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Service Classifications');

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
