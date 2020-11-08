<?php
class ProjectExpensesHelper extends AppHelper {
	public $helpers = array('Html', 'LimitlessTheme.Alerts', 'FieldData.FieldData', 'FormReload');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function projectField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, $this->FormReload->triggerOptions());
	}
}