<?php
namespace Suggestion\Package\ThirdParty;
use Suggestion\Package\Legal\PersonalDataAct;

class Datacenter extends BasePackage {
	public $alias = 'Datacenter';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Datacenter');

		$this->data = array(
			'name' => __('Datacenter'),
			'description' => __('Thrid party facility that provides racking space for computing devices'),
			'third_party_type_id' => 1,
			'legal_id' => array(new PersonalDataAct()),
			'sponsor_id' => array(ADMIN_ID) 
		);
	}
}
