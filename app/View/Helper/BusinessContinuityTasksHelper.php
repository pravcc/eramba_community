<?php
App::uses('AppHelper', 'View/Helper');

class BusinessContinuityTasksHelper extends AppHelper
{

	public $helpers = ['NotificationSystem', 'Html', 'Ajax', 'Eramba', 'FieldData.FieldData', 'FormReload'];
	public $settings = [];

	public function __construct(View $view, $settings = array())
	{
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function getStatuses($item, $addModelKey = false)
	{
		$statuses = array();

		$item = $this->processItemArray($item, 'BusinessContinuityTask');

		$statuses = array_merge($statuses, $this->NotificationSystem->getStatuses($item));

		return $this->processStatuses($statuses);
	}

	public function businessContinuityPlanField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, $this->FormReload->triggerOptions());
	}
}