<?php
/**
 * Trait helper class for Widgets - Comments, Attachments.
 */
trait SidebarWidgetTrait {

	public function widgetBeforeFind($query)
	{
		return $this->_manageReviews($query);
	}

	// solution for a not singular model name values for Reviews
	protected function _manageReviews($query)
	{
		$reviewModels = [
			'RiskReview',
			'ThirdPartyRiskReview',
			'BusinessContinuityReview',
			'AssetReview',
			'SecurityPolicyReview'
		];
		
		$conds = isset($query['conditions'][$this->alias . '.model']);
		$conds = $conds && in_array($query['conditions'][$this->alias . '.model'], $reviewModels);
		if ($conds) {
			$modelConds = $query['conditions'][$this->alias . '.model'];
			if (!is_array($modelConds)) {
				$query['conditions'][$this->alias . '.model'] = array($modelConds);
			}

			$query['conditions'][$this->alias . '.model'][] = 'Review';
		}

		return $query;
	}

}