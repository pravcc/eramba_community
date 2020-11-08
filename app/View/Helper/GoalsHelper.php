<?php
App::uses('ErambaHelper', 'View/Helper');
class GoalsHelper extends ErambaHelper
{
	public $helpers = array('Html', 'Form', 'FieldData.FieldData', 'Ux');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	public function getStatusArr($item, $allow = '*') {
		$item = $this->processItemArray($item, 'Goal');
		$statuses = array();

		if ($this->getAllowCond($allow, 'metrics_last_missing') && $item['Goal']['metrics_last_missing']) {
			$statuses[$this->getStatusKey('metrics_last_missing')] = array(
				'label' => __('Last Performance Review missing'),
				'type' => 'warning'
			);
		}

		if ($this->getAllowCond($allow, 'ongoing_corrective_actions') && $item['Goal']['ongoing_corrective_actions']) {
			$statuses[$this->getStatusKey('ongoing_corrective_actions')] = array(
				'label' => __('Ongoing Corrective Actions'),
				'type' => 'improvement'
			);
		}

		return $statuses;
	}

	public function getStatuses($item, $options = array()) {
		$options = $this->processStatusOptions($options);
		$statuses = $this->getStatusArr($item, $options['allow']);
		
		return $this->styleStatuses($statuses, $options);
	}

	public function auditsField(FieldDataEntity $Field)
	{
		return $this->Ux->handleAuditCalendarFields($Field, [
			'controller' => 'goals'
		]);
	}
}
