<?php
App::uses('RenderProcessor', 'ObjectRenderer.View/Renderer');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('AdvancedFiltersQuery', 'Lib/AdvancedFilters');
App::uses('AdvancedFilterValue', 'AdvancedFilters.Model');
App::uses('FilterAdapter', 'AdvancedFilters.Lib/QueryAdapter');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('Inflector', 'Utility');

/**
 * Renderer class for active filters.
 */
class ActiveFiltersRenderProcessor extends RenderProcessor
{
	/**
	 * Render content to output.
	 * 
	 * @param  OutputBuilder $output Output builder.
	 * @param  object $subject Subject parameters.
	 * @return void    
	 */
	public function render(OutputBuilder $output, $subject)
	{
		$fieldSet = $subject->filterObject->FilterFieldSet();

		$activeFilters = [];

		$parseFindOptions = $subject->filterObject->parseFindOptions();
		$limit = $subject->filterObject->getFilterValues('_limit');

		// limit
		if ($limit !== null) {
			if ($limit == AdvancedFilterValue::LIMIT_UNLIMITED) {
				$AdvancedFilterValue = ClassRegistry::init('AdvancedFilters.AdvancedFilterValue');
				$limitOptions = $AdvancedFilterValue->getLimitOptions();

				$limit = $limitOptions[$limit];
			}

			$label = __('Limit') . ' ' . $this->_renderComparison('IS', $subject) . ' ' . $limit;
			$activeFilters[] = $subject->view->Labels->info($label, [
				'class' => ['active-filter-label']
			]);
		}	

		// order
		if (isset($parseFindOptions['order'])) {
			$AdvancedFilterValue = ClassRegistry::init('AdvancedFilters.AdvancedFilterValue');
			$direction = $AdvancedFilterValue->getOrderDirections()[$subject->filterObject->getFilterValues('_order_direction')];

			$model = $subject->filterObject->getModel();
			$column = $model->getFieldDataEntity($subject->filterObject->getFilterValues('_order_column'))->getLabel();

			$label = __('Order') . ' ' . $this->_renderComparison('BY', $subject) . ' ' . $column;
			$activeFilters[] = $subject->view->Labels->info($label, [
				'class' => ['active-filter-label']
			]);

			$label = __('Sort') . ' ' . $this->_renderComparison('IS', $subject) . ' ' . $direction;
			$activeFilters[] = $subject->view->Labels->info($label, [
				'class' => ['active-filter-label']
			]);
		}

		foreach ($subject->filterObject->FilterFieldSet() as $field => $FilterField) {
			if ($FilterField->isActive()) {
				$label = $this->_activeLabel($FilterField, $subject);
				$activeFilters[] = $subject->view->Labels->info($label, [
					'class' => ['active-filter-label']
				]);
			}
		}

		if (!empty($activeFilters)) {
			$output->template(implode(' ', $activeFilters));
		}
	}

	/**
	 * Get active label content.
	 * 
	 * @param  OutputBuilder $output Output builder.
	 * @param  object $subject Subject parameters.
	 * @return string
	 */
	protected function _activeLabel($FilterField, $subject)
	{
		// special case
		if ($FilterField->getFieldName() == 'deleted'
			&& $FilterField->getComparisonType() == FilterAdapter::COMPARISON_EQUAL
			&& $FilterField->getValue() == 1
		) {
			$label = __('Deleted');
		}
		// default fallback
		else {
			$label = $FilterField->getLabel();
			$label .= ' ' . $this->_getComparison($FilterField, $subject);
			$label .= ' ' . $this->_getValue($FilterField, $subject);
		}

		return $label;
	}

	/**
	 * Get comparison content.
	 * 
	 * @param  OutputBuilder $output Output builder.
	 * @param  object $subject Subject parameters.
	 * @return string
	 */
	protected function _getComparison($FilterField, $subject)
	{
		$comparison = '';

		$type = $FilterField->getType();

		$typeClass = AdvancedFiltersQuery::getTypeClass($FilterField->getType());

		if (isset($typeClass::getComparisonTypes()[$FilterField->getComparisonType()])) {
			$comparison = $typeClass::getComparisonTypes()[$FilterField->getComparisonType()];
		}

		return $this->_renderComparison($comparison, $subject);
	}

	/**
	 * Renders a comparison text using view object.
	 *
	 * @return string
	 */
	protected function _renderComparison($comparison, $subject)
	{
		return $subject->view->Html->tag('span', $comparison, [
			'class' => 'comparison'
		]);
	}

	/**
	 * Get value content.
	 * 
	 * @param  OutputBuilder $output Output builder.
	 * @param  object $subject Subject parameters.
	 * @return string
	 */
	protected function _getValue($FilterField, $subject)
	{
		$values = [];

		$rawValues = (array) $FilterField->getValue();

		$specialValues = FilterAdapter::getSpecialValueLabels();

		$data = $subject->view->get(Inflector::slug($FilterField->getFieldName()) . '_data');

		foreach ($rawValues as $key => $rawValue) {
			if (isset($specialValues[$rawValue])) {
				$values[] = $specialValues[$rawValue];
			}
			elseif (is_array($data) && !empty($data) && isset($data[$rawValue])) {
				$values[] = $data[$rawValue];
			}
			else {
				$values[] = $rawValue;
			}
		}

		$glue = $subject->view->Html->tag('span', $this->_getGlue($FilterField), [
			'class' => ['glue']
		]);

		return implode($glue, $values);
	}

	/**
	 * Get glue for multiple values.
	 * 
	 * @param  OutputBuilder $output Output builder.
	 * @return string
	 */
	protected function _getGlue($FilterField)
	{
		$glue = __(' OR ');

		$compType = $FilterField->getComparisonType();

		$andCompTypes = [
			FilterAdapter::COMPARISON_NOT_IN,
			FilterAdapter::COMPARISON_ALL_IN,
			FilterAdapter::COMPARISON_ONLY_IN,
			FilterAdapter::COMPARISON_NOT_ONLY_IN
		];

		if (in_array($FilterField->getComparisonType(), $andCompTypes)) {
			$glue = __(' AND ');
		}

		return $glue;
	}
}