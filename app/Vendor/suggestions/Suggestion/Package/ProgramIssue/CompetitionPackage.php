<?php
namespace Suggestion\Package\ProgramIssue;

class CompetitionPackage extends BasePackage {
	public $alias = 'CompetitionPackage';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Competition');
		// $this->description = __('Description');

		$this->data = array(
			'name' => $this->name,
			'issue_source' => PROGRAM_ISSUE_EXTERNAL,

			// lookup /Config/bootstrap.php -> functions getInternalTypes() and getExternalTypes()
			// usage as array with numbered keys of the types
			'types' => array(5, 6),

			'description' => __('Our competitors have invested largely in innovation and have increased their competitive advantage by attracting customers such as startups.'),
			'status' => PROGRAM_ISSUE_DRAFT,
		);
	}
}
