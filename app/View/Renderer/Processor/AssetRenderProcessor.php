<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('ClassificationRenderProcessor', 'View/Renderer/Processor');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('SecurityIncident', 'Model');

class AssetRenderProcessor extends SectionRenderProcessor
{
    public function objectStatusExpiredReviews(OutputBuilder $output, $subject)
    {
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('assetReviews', [
			'foreign_key' => $subject->item->getPrimary(),
			'ObjectStatus_expired' => 1
		]));
    }

    public function assetClassification(OutputBuilder $output, $subject)
    {
        (new ClassificationRenderProcessor())->render($output, $subject);
    }

    public function objectStatusOngoingIncident(OutputBuilder $output, $subject)
    {
    	$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('securityIncidents', [
			'Asset' => $subject->item->getPrimary(),
			'security_incident_status_id' => SecurityIncident::STATUS_ONGOING
		]));
	}
}