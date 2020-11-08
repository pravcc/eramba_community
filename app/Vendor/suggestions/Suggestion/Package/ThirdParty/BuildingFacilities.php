<?php
namespace Suggestion\Package\ThirdParty;

class BuildingFacilities extends BasePackage {
	public $alias = 'BuildingFacilities';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Building Facilities');

		$this->data = array(
			'name' => __('Building Facilities'),
			'description' => __('Third party organisation that looks after the building services'),
			'third_party_type_id' => 2,
			'legal_id' => '',
			'sponsor_id' => ''
		);
	}
}
