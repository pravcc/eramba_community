<?php
App::uses('BaseRiskRenderProcessor', 'View/Renderer/Processor');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');

class BusinessContinuityRenderProcessor extends BaseRiskRenderProcessor
{
	protected $_foreignKey = 'BusinessContinuity';

	public function objectStatusExpiredReviews(OutputBuilder $output, $subject)
	{
		$this->_statusFilter($output, $subject, __('Show'), $subject->view->AdvancedFilters->filterUrl('businessContinuityReviews', [
			'foreign_key' => $subject->item->getPrimary(),
			'ObjectStatus_expired' => 1
		]));
	}
}