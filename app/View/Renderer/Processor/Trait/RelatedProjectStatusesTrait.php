<?php
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('Project', 'Model');

trait RelatedProjectStatusesTrait  {

	public function objectStatusProjectExpired(OutputBuilder $output, $subject)
	{
		$this->_statusFilterAll($output, $subject, __('Show'), function ($subject, $value) {
			$data = $value['item']->getData();

			$projectIds = Hash::extract($data, 'Project.{n}.id');

            return $subject->view->AdvancedFilters->filterUrl('projects', [
    			'id' => $projectIds,
    			'ObjectStatus_expired' => 1
		    ]);
        });
    }

	public function objectStatusProjectExpiredTasks(OutputBuilder $output, $subject)
	{
		$this->_statusFilterAll($output, $subject, __('Show'), function ($subject, $value) {
			$data = $value['item']->getData();

			$projectIds = Hash::extract($data, 'Project.{n}.id');

            return $subject->view->AdvancedFilters->filterUrl('projects', [
    			'id' => $projectIds,
    			'ObjectStatus_expired_tasks' => 1
		    ]);
        });
    }

    public function objectStatusProjectOngoing(OutputBuilder $output, $subject)
    {
        $this->_statusFilterAll($output, $subject, __('Show'), function ($subject, $value) {
            $data = $value['item']->getData();

            $projectIds = Hash::extract($data, 'Project.{n}.id');

            return $subject->view->AdvancedFilters->filterUrl('projects', [
                'id' => $projectIds,
                'project_status_id' => Project::STATUS_ONGOING
            ]);
        });
    }

    public function objectStatusProjectClosed(OutputBuilder $output, $subject)
    {
    	$this->_statusFilterAll($output, $subject, __('Show'), function ($subject, $value) {
			$data = $value['item']->getData();

			$projectIds = Hash::extract($data, 'Project.{n}.id');

            return $subject->view->AdvancedFilters->filterUrl('projects', [
    			'id' => $projectIds,
    			'project_status_id' => Project::STATUS_COMPLETED
		    ]);
        });
    }

    public function objectStatusProjectPlanned(OutputBuilder $output, $subject)
    {
    	$this->_statusFilterAll($output, $subject, __('Show'), function ($subject, $value) {
			$data = $value['item']->getData();

			$projectIds = Hash::extract($data, 'Project.{n}.id');

            return $subject->view->AdvancedFilters->filterUrl('projects', [
    			'id' => $projectIds,
    			'project_status_id' => Project::STATUS_PLANNED
		    ]);
        });
    }

    public function objectStatusProjectNoUpdates(OutputBuilder $output, $subject)
    {
		$this->_statusFilterAll($output, $subject, __('Show'), function ($subject, $value) {
			$data = $value['item']->getData();

			$projectIds = Hash::extract($data, 'Project.{n}.id');

            return $subject->view->AdvancedFilters->filterUrl('projects', [
    			'id' => $projectIds,
    			'ObjectStatus_no_updates' => 1
		    ]);
        });
    }
}