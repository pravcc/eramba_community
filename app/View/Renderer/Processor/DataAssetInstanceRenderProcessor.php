<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('AbstractQuery', 'Lib.AdvancedFilters/Query');
App::uses('Project', 'Model');

class DataAssetInstanceRenderProcessor extends SectionRenderProcessor
{
    public function objectStatusAssetMissingReview(OutputBuilder $output, $subject)
    {
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('assets', [
			'id' => $subject->item->asset_id,
			'ObjectStatus_expired_reviews' => 1
		]));
    }

	public function objectStatusControlsWithIssues(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityServices', [
			'DataAsset' => $subject->item->DataAsset->getPrimaryKeys(),
			'ObjectStatus_control_with_issues' => 1
		]));
	}

	public function objectStatusControlsWithFailedAudits(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityServices', [
			'DataAsset' => $subject->item->DataAsset->getPrimaryKeys(),
			'ObjectStatus_audits_last_not_passed' => 1
		]));
	}

	public function objectStatusControlsWithMissingAudits(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityServices', [
			'DataAsset' => $subject->item->DataAsset->getPrimaryKeys(),
			'ObjectStatus_audits_last_missing' => 1
		]));
	}

	public function objectStatusPoliciesWithMissingReviews(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityPolicies', [
			'DataAsset' => $subject->item->DataAsset->getPrimaryKeys(),
			'ObjectStatus_expired_reviews' => 1
		]));
	}

	public function objectStatusRiskExpiredReviews(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('risks', [
			'DataAsset' => $subject->item->DataAsset->getPrimaryKeys(),
			'ObjectStatus_expired_reviews' => 1
		]));
	}

	public function objectStatusThirdPartyRiskExpiredReviews(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('thirdPartyRisks', [
			'DataAsset' => $subject->item->DataAsset->getPrimaryKeys(),
			'ObjectStatus_expired_reviews' => 1
		]));
	}

	public function objectStatusBusinessContinuityExpiredReviews(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('businessContinuities', [
			'DataAsset' => $subject->item->DataAsset->getPrimaryKeys(),
			'ObjectStatus_expired_reviews' => 1
		]));
	}

	public function objectStatusProjectExpired(OutputBuilder $output, $subject)
	{
    	$data = $subject->item->getData();

		$projectIds = Hash::extract($data, 'DataAsset.{n}.Project.{n}.id');

    	$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('projects', [
			'id' => $projectIds,
			'ObjectStatus_expired' => 1
		]));
    }

	public function objectStatusProjectExpiredTasks(OutputBuilder $output, $subject)
	{
    	$data = $subject->item->getData();

		$projectIds = Hash::extract($data, 'DataAsset.{n}.Project.{n}.id');

    	$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('projects', [
			'id' => $projectIds,
			'ObjectStatus_expired_tasks' => 1
		]));
    }

    public function objectStatusProjectOngoing(OutputBuilder $output, $subject)
    {
        $this->_statusFilterAll($output, $subject, __('Show'), function ($subject, $value) {
            $data = $value['item']->getData();

            $projectIds = Hash::extract($data, 'DataAsset.{n}.Project.{n}.id');

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

			$projectIds = Hash::extract($data, 'DataAsset.{n}.Project.{n}.id');

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

			$projectIds = Hash::extract($data, 'DataAsset.{n}.Project.{n}.id');

            return $subject->view->AdvancedFilters->filterUrl('projects', [
    			'id' => $projectIds,
    			'project_status_id' => Project::STATUS_PLANNED
		    ]);
        });
    }

    public function objectStatusProjectNoUpdates(OutputBuilder $output, $subject)
    {
    	$data = $subject->item->getData();

		$projectIds = Hash::extract($data, 'DataAsset.{n}.Project.{n}.id');

    	$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('projects', [
			'id' => $projectIds,
			'ObjectStatus_no_updates' => 1
		]));
    }

}