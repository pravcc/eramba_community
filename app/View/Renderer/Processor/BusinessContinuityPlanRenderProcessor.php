<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('AbstractQuery', 'Lib.AdvancedFilters/Query');
App::uses('BusinessContinuityPlanAudit', 'Model');

class BusinessContinuityPlanRenderProcessor extends SectionRenderProcessor
{
    public function objectStatusAuditsLastPassed(OutputBuilder $output, $subject)
    {
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('businessContinuityPlanAudits', [
			'business_continuity_plan_id' => $subject->item->getPrimary(),
			'result' => BusinessContinuityPlanAudit::RESULT_FAILED
		]));
    }

    public function objectStatusAuditsLastMissing(OutputBuilder $output, $subject)
    {
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('businessContinuityPlanAudits', [
			'business_continuity_plan_id' => $subject->item->getPrimary(),
			'ObjectStatus_audit_missing' => 1
		]));
    }
}
