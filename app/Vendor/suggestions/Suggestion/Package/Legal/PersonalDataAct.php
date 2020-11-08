<?php
namespace Suggestion\Package\Legal;

class PersonalDataAct extends BasePackage {
	public $alias = 'PersonalDataAct';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Personal Data Act');

		$this->data = array(
			'name' => $this->name,
			'description' => __('The regulations related to personal data privacy'),
			'legal_advisor_id' => array(ADMIN_ID)
		);
	}
}
