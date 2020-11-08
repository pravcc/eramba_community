<?php
App::uses('AppHelper', 'View/Helper');

class ProgramScopesHelper extends AppHelper {
	public $helpers = ['Html', 'FieldData.FieldData', 'LimitlessTheme.Alerts'];
	public $settings = array();
	
	public function __construct(View $view, $settings = array())
	{
		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	public function statusField(FieldDataEntity $Field)
	{
		$types = getProgramScopeStatuses();
		if (!empty($this->_View->viewVars['hasCurrent'])) {
			unset($types[PROGRAM_SCOPE_CURRENT]);
		}

		$out = $this->FieldData->input($Field, [
            'options' => $types
        ]);

		if (!empty($this->_View->viewVars['hasCurrent'])) {
			 $out .= $this->Alerts->danger(__('Current scope already exists and therefore cannot be selected as a status.'));
		}

        return $out;
	}
}
