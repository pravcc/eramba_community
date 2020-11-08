<?php
App::uses('RenderProcessor', 'ObjectRenderer.View/Renderer');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');
App::uses('RiskAppetite', 'Model');
App::uses('RiskClassification', 'Model');

class RiskScoreRenderProcessor extends RenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
		if ($subject->field->getFieldName() == 'risk_score') {
			$itemKey = $output->getKey($subject->item, $subject->field);

			$item = $subject->item;
			$view = $subject->view;
			$appetiteMethod = $this->_getAppetiteMethod($subject);

			$value = $item->risk_score;
			$html = $value;
			if ($item->risk_score_formula) {
				$text = $value . ' ' . $view->Icon->icon('info22');
				$html = $view->Popovers->top($text, $item->risk_score_formula, __('Risk Score Calculation Formula'));
			}

			if ($appetiteMethod == RiskAppetite::TYPE_THRESHOLD) {
				$html = $view->RiskAppetites->label($item->getRiskAppetiteThreshold()[RiskClassification::TYPE_ANALYSIS], $html);
			}

			$output->label([$itemKey => $html]);
		}

		if ($subject->field->getFieldName() == 'residual_risk') {
			$itemKey = $output->getKey($subject->item, $subject->field);
			
			$item = $subject->item;
			$view = $subject->view;
			$appetiteMethod = $this->_getAppetiteMethod($subject);

			$value = $item->residual_risk;
			$html = $value;

			$formula = getResidualRiskFormula($item->residual_score, $item->risk_score);
			
			if ($appetiteMethod == RiskAppetite::TYPE_THRESHOLD) {
				$formula = $item->residual_risk_formula;
			}

			if (!empty($formula)) {
				$text = $value . ' ' . $view->Icon->icon('info22');
				$html = $view->Popovers->top($text, $formula, __('Residual Score Calculation Formula'));
			}

			if ($appetiteMethod == RiskAppetite::TYPE_THRESHOLD) {
				$html = $view->RiskAppetites->label($item->getRiskAppetiteThreshold()[RiskClassification::TYPE_TREATMENT], $html);
			}

			$output->label([$itemKey => $html]);
		}
	}

	protected function _getAppetiteMethod($subject)
	{
		$appetiteMethod = $subject->view->get('appetiteMethod');

		if ($appetiteMethod === null) {
			$appetiteMethod = ClassRegistry::init('RiskAppetite')->getCurrentType();
		}

		return $appetiteMethod;
	}

}