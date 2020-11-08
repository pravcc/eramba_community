<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('ClassificationRenderProcessor', 'View/Renderer/Processor');
App::uses('RelatedProjectStatusesTrait', 'View/Renderer/Processor/Trait');
App::uses('SecurityService', 'Model');
App::uses('SecurityIncident', 'Model');

class BaseRiskRenderProcessor extends SectionRenderProcessor
{
	use RelatedProjectStatusesTrait;
	
	protected $_foreignKey = '';

	public function riskClassification(OutputBuilder $output, $subject)
    {
		(new ClassificationRenderProcessor())->render($output, $subject);
    }

    public function riskClassificationTreatment(OutputBuilder $output, $subject)
    {
		$this->riskClassification($output, $subject);
    }

	public function objectStatusRiskExceptionExpired(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('riskExceptions', [
			$this->_foreignKey => $subject->item->getPrimary(),
			'ObjectStatus_expired' => 1
		]));
	}

	public function objectStatusControlWithIssues(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityServices', [
			$this->_foreignKey => $subject->item->getPrimary(),
			'ObjectStatus_control_with_issues' => 1
		]));
	}

	public function objectStatusControlInDesign(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityServices', [
			$this->_foreignKey => $subject->item->getPrimary(),
			'security_service_type_id' => SECURITY_SERVICE_DESIGN
		]));
	}

	public function objectStatusOngoingIncident(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityIncidents', [
			$this->_foreignKey => $subject->item->getPrimary(),
			'security_incident_status_id' => SecurityIncident::STATUS_ONGOING
		]));
	}

	public function objectStatusAuditsLastNotPassed(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityServices', [
			$this->_foreignKey => $subject->item->getPrimary(),
			'ObjectStatus_audits_last_not_passed' => 1
		]));
	}

	public function objectStatusAuditsLastMissing(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityServices', [
			$this->_foreignKey => $subject->item->getPrimary(),
			'ObjectStatus_audits_last_missing' => 1
		]));
	}

	public function objectStatusMaintenancesLastMissing(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityServices', [
			$this->_foreignKey => $subject->item->getPrimary(),
			'ObjectStatus_maintenances_last_missing' => 1
		]));
	}
}