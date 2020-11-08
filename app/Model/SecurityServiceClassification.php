<?php
class SecurityServiceClassification extends AppModel {
	public $actsAs = array(
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'criteria'
			)
		),
		'Containable'
	);

	public $belongsTo = array(
		'SecurityService'
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Security Service Classifications');

		$this->fieldData = array(
            'name' => array(
                'label' => __('Name'),
                'editable' => true
            ),
        );
		
		parent::__construct($id, $table, $ds);
	}
}
