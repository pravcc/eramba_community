<?php
class SecurityServiceType extends AppModel {
	public $displayField = 'name';
	
	public $actsAs = array(
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name'
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

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Security Service Types');

		$this->fieldGroupData = [
            'default' => [
                'label' => __('General')
            ],
        ];

        $this->fieldData = [
            'name' => [
                'label' => __('Name'),
                'description' => __(''),
                'editable' => true
            ],
        ];
		
		parent::__construct($id, $table, $ds);
	}
}
