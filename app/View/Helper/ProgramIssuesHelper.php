<?php
App::uses('ErambaHelper', 'View/Helper');
App::uses('ClassRegistry', 'Utility');
App::uses('ProgramIssue', 'Model');

class ProgramIssuesHelper extends ErambaHelper
{
	public $helpers = ['Html', 'FieldData.FieldData', 'FormReload'];
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		$this->helpers[] = 'ErambaTime';
		$this->helpers[] = 'NotificationSystem';

		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	public function getItemTypesArr($item) {
		$item = $this->processItemArray($item, 'ProgramIssue');

		if ($item['ProgramIssue']['issue_source'] == PROGRAM_ISSUE_INTERNAL) {
			$types = getInternalTypes();
		}
		else {
			$types = getExternalTypes();
		}

		$itemTypes = array();
		foreach ($item['ProgramIssueType'] as $type) {
			$itemTypes[] = $types[$type['type']];
		}

		return $itemTypes;
	}

	public function getItemTypes($item) {
		$item = $this->processItemArray($item, 'ProgramIssue');

		$itemTypes = $this->getItemTypesArr($item);

		if (!empty($itemTypes)) {
			return implode(', ', $itemTypes);
		}
		return '-';
	}

	public function issueSourceField(FieldDataEntity $Field)
	{
        $options = $this->FormReload->triggerOptions(['field' => $Field]);

        return $this->FieldData->input($Field, $options);
	}
}
