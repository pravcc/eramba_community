<?php
namespace Suggestion\Package\ProgramIssue;

class PoliticalConstrainsPackage extends BasePackage {
	public $alias = 'PoliticalConstrainsPackage';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Political Constrains');
		// $this->description = __('Description');

		$this->data = array(
			'name' => $this->name,
			'issue_source' => PROGRAM_ISSUE_EXTERNAL,

			// lookup /Config/bootstrap.php -> functions getInternalTypes() and getExternalTypes()
			// usage as array with numbered keys of the types
			'types' => array(1, 2),

			'description' => __('Political issues in the region pose a constant threat to the business in terms of savotage, political coalitions, hostile government lead take overs.'),
			'status' => PROGRAM_ISSUE_DRAFT,
		);
	}
}
