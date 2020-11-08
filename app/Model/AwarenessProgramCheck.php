<?php
class AwarenessProgramCheck extends AppModel {
	public $useTable = false;

	// public $validate = array(
	// 	'email' => array(
	// 		'notEmpty' => array(
	// 			'rule' => 'notBlank',
	// 			'required' => true,
	// 			'allowEmpty' => false,
	// 			'message' => 'User must be selected'
	// 		),
	// 		'email' => array(
	// 			'rule' => 'email',
	// 			'required' => true,
	// 			'allowEmpty' => false,
	// 			'message' => 'Email address missing'
	// 		)
	// 	)
	// );

	public function __construct($id = false, $table = null, $ds = null) {
        $this->label = __('Awareness Program Demo');

        $this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
        ];

        $this->fieldData = [
        	'ldap_connector_id' => [
        		'type' => 'hidden'
        	],
        	'ldap_groups' => [
        		'type' => 'hidden'
        	],
            'ldap_user' => [
                'type' => 'select',
                'label' => __('LDAP User'),
                'description' => __('Choose user that will be used for validation')
                // 'empty' => __('Select a user')
            ]
        ];

        parent::__construct($id, $table, $ds);
    }
}