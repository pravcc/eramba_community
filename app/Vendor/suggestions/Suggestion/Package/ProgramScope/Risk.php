<?php
namespace Suggestion\Package\ProgramScope;

class Risk extends BasePackage {
	public $alias = 'Risk';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Risk for Production');
		// $this->description = __('Description');

		$this->data = array(
			'version' => $this->name,
			'description' => __('The goal of this program is to manage risk according to the ISO 27001:2005 standard for our line of production based in Spain.'),
			'status' => PROGRAM_SCOPE_DRAFT
		);
	}
}
