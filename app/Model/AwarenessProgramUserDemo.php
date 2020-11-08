<?php
class AwarenessProgramUserDemo extends AppModel {
	public $useTable = false;

	public $validate = array(
		'email' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Choose a user for this demo'
			),
			// 'email' => array(
			// 	'rule' => 'email',
			// 	'required' => true,
			// 	'allowEmpty' => false,
			// 	'message' => 'Email address missing'
			// )
		)
	);

	public function __construct($id = false, $table = null, $ds = null) {
        $this->label = __('Awareness Program Demo');

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
        ];

        $this->fieldData = [
            'email' => [
                'type' => 'select',
                'label' => __('User'),
                'empty' => __('Select a user')
            ]
        ];

        parent::__construct($id, $table, $ds);
    }
}