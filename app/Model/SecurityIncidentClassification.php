<?php
class SecurityIncidentClassification extends AppModel {
	public $actsAs = array(
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'criteria'
			)
		)
	);

	public $mapping = array(
		'titleColumn' => 'name',
		//'logRecords' => true
	);
	
	// public $validate = array(
	// 	'name' => array(
	// 		'rule' => 'notBlank',
	// 		'required' => true,
	// 		'allowEmpty' => false
	// 	)
	// );

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Security Incident Classifications');

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
