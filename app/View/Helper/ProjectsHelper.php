<?php
App::uses('ErambaHelper', 'View/Helper');
class ProjectsHelper extends ErambaHelper {
	public $helpers = array('NotificationSystem', 'Html', 'AdvancedFilters', 'Taggable');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function getStatusArr($item, $allow = '*') {
		$item = $this->processItemArray($item, 'Project');
		$statuses = array();

		if ($this->getAllowCond($allow, 'expired') && $item['Project']['expired']) {
			$statuses[$this->getStatusKey('expired')] = array(
				'label' => __('Improvement Project Expired'),
				'type' => 'danger'
			);
		}

		if ($this->getAllowCond($allow, 'over_budget') && $item['Project']['over_budget']) {
			$statuses[$this->getStatusKey('over_budget')] = array(
				'label' => __('Improvement Project over Budget'),
				'type' => 'danger'
			);
		}

		if ($this->getAllowCond($allow, 'expired_tasks') && $item['Project']['expired_tasks'] > 0) {
			$statuses[$this->getStatusKey('expired_tasks')] = array(
				'label' => __('Improvement Project with Expired Tasks'),
				'type' => 'warning'
			);
		}

		return $statuses;
	}

	public function getTags($item) {
		return $this->Taggable->showList($item, [
			'notFoundCallback' => [$this->Taggable, 'notFoundBlank']
		]);
	}

	public function getStatuses($item, $options = array()) {
		$options = $this->processStatusOptions($options);
		$statuses = $this->getStatusArr($item, $options['allow']);

		return $this->styleStatuses($statuses, $options);
	}

	public function outputTasks($data, $options = array()) {
        $link = $this->AdvancedFilters->getItemFilteredLink(__('List Tasks'), 'ProjectAchievement', $data, array(
            'key' => 'project_id',
        ), $options);

        return $link;
    }

    public function outputExpenses($data, $options = array()) {
        $link = $this->AdvancedFilters->getItemFilteredLink(__('List Expenses'), 'ProjectExpense', $data, array(
            'key' => 'project_id',
        ), $options);

        return $link;
    }
}