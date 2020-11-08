<?php
namespace Suggestion\Package\ProgramScope;

class CompliancePackage extends BasePackage {
	public $alias = 'CompliancePackage';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Compliance');
		// $this->description = __('Description');

		$this->data = array(
			'version' => $this->name,
			'description' => __('The goal of this program is to ensure compliance with local and international regulators in all line of business across all worldwide locations.'),
			'status' => PROGRAM_SCOPE_DRAFT
		);
	}
}
