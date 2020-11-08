<?php
App::uses('AppHelper', 'View/Helper');
class GoalAuditsHelper extends AppHelper {
	public $helpers = array('NotificationSystem', 'Html', 'FieldData.FieldData', 'FormReload');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function goalField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, $this->FormReload->triggerOptions());
	}

	public function getStatuses($item, $addModelKey = false) {
		$statuses = array();

		$item = $this->processItemArray($item, $addModelKey);

		$statuses = array_merge($statuses, $this->NotificationSystem->getStatuses($item));

		return $this->processStatuses($statuses);
	}

}