<?php
App::uses('BaseRiskRenderProcessor', 'View/Renderer/Processor');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('SecurityIncident', 'Model');

class RiskRenderProcessor extends BaseRiskRenderProcessor
{
	protected $_foreignKey = 'Risk';

	public function objectStatusExpiredReviews(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('riskReviews', [
			'foreign_key' => $subject->item->getPrimary(),
			'ObjectStatus_expired' => 1
		]));
	}

	public function objectStatusOngoingIncident(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityIncidents', [
			'AssetRisk' => $subject->item->getPrimary(),
			'security_incident_status_id' => SecurityIncident::STATUS_ONGOING
		]));
	}
}