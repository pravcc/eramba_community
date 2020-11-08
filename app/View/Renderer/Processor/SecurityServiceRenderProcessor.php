<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('SecurityIncident', 'Model');
App::uses('Project', 'Model');
App::uses('SecurityServiceIssue', 'Model');
App::uses('RelatedProjectStatusesTrait', 'View/Renderer/Processor/Trait');

class SecurityServiceRenderProcessor extends SectionRenderProcessor
{
	use RelatedProjectStatusesTrait;

	public function render(OutputBuilder $output, $subject)
	{
		parent::render($output, $subject);
	}

	public function objectStatusAuditsLastMissing(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityServiceAudits', [
			'security_service_id' => $subject->item->getPrimary(),
			'ObjectStatus_audit_missing' => 1
		]));
	}

	public function objectStatusMaintenancesLastMissing(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityServiceMaintenances', [
			'security_service_id' => $subject->item->getPrimary(),
			'ObjectStatus_maintenance_missing' => 1
		]));
	}

	public function objectStatusAuditsLastNotPassed(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityServiceAudits', [
			'security_service_id' => $subject->item->getPrimary(),
			'result' => 0
		]));
	}

	public function objectStatusMaintenancesLastNotPassed(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityServiceMaintenances', [
			'security_service_id' => $subject->item->getPrimary(),
			'result' => 0
		]));
	}

    public function objectStatusOngoingIncident(OutputBuilder $output, $subject) {
    	$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityIncidents', [
			'SecurityService' => $subject->item->getPrimary(),
			'security_incident_status_id' => SecurityIncident::STATUS_ONGOING
		]));
    }

    public function objectStatusControlWithIssues(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityServiceIssues', [
			'foreign_key' => $subject->item->getPrimary(),
			'status' => SecurityServiceIssue::STATUS_OPEN
		]));
	}
}