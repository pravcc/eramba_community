<?php
App::uses('AppHelper', 'View/Helper');
App::uses('DashboardKpi', 'Dashboard.Model');
App::uses('DashboardKpiThreshold', 'Dashboard.Model');
App::uses('Dashboard', 'Dashboard.Lib');

class DashboardKpiHelper extends AppHelper {
	public $helpers = ['Html', 'Ux', 'AdvancedFilters', 'Eramba'];

	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->DashboardKpi = ClassRegistry::init('Dashboard.DashboardKpi');
	}

	public function getThresholdParams($item)
	{
		$class = null;
		$style = null;
		$dataAttrs = null;
		if ($this->hasThreshold($item)) {
			$threshold = $this->getThreshold($item);

			$class = 'dashboard-has-threshold bs-popover';
			$style = 'background-color:' . $threshold['color'];
			$dataAttrs = 'data-placement="top" data-trigger="hover" data-title="' . $threshold['title'] . '" data-content="' . $threshold['description'] . '" data-container="body"';
		}

		return compact('class', 'style', 'dataAttrs');
	}

	public function hasThreshold($item)
	{
		return (bool) $this->_evaluateThreshold($item);
	}

	public function getThreshold($item)
	{
		if (!$threshold = $this->_evaluateThreshold($item)) {
			return false;
		}

		return $threshold;
	}

	protected function _evaluateThreshold($item)
	{
		$value = $item['DashboardKpi']['value'];

		foreach ($item['DashboardKpiThreshold'] as $threshold) {
			$min = $threshold['min'];
			$max = $threshold['max'];
			$percentage = $threshold['percentage'];
			$color = $threshold['color'];

			// range threshold type evaluation
			$rangeConds = $threshold['type'] == DashboardKpiThreshold::TYPE_RANGE;
			$rangeConds = $rangeConds && ($value >= $min && $value <= $max);

			// percentage threshold type evaluation
			$changeConds = $threshold['type'] == DashboardKpiThreshold::TYPE_CHANGE;
			$changeConds = $changeConds && $this->_evaluateChange($percentage, $value, $item);

			if ($rangeConds || $changeConds) {
				return $threshold;
			}
		}

		return false;
	}

	protected function _evaluateChange($percentage, $value, $item)
	{
		if (isset($item['DashboardKpiLastLog']['value'])) {
			$evaluation = $this->_evaluateChangeFormula($value, $item['DashboardKpiLastLog']['value']);

			return $evaluation >= $percentage;
		}

		return false;
	}

	protected function _evaluateChangeFormula($currentValue, $previousValue)
	{
		// division by zero handler
		if ($previousValue == 0) {
			return 0;
		}

		$increase = $currentValue - $previousValue;
		$percentage = $increase / $previousValue * 100;

		return abs($percentage);
	}

	public function thresholdCell($item)
	{
		$title = $item['DashboardKpi']['title'];
		$value = $item['DashboardKpi']['value'];

		if (empty($item['DashboardKpiThreshold'])) {
			return $title;
		}

		foreach ($item['DashboardKpiThreshold'] as $threshold) {
			$min = $threshold['min'];
			$max = $threshold['max'];
			$percentage = $threshold['percentage'];
			$color = $threshold['color'];

			$rangeConds = $threshold['type'] == DashboardKpiThreshold::TYPE_RANGE;
			$rangeConds &= $value > $min && $value < $max;
			if ($rangeConds) {
				$finalColor = $color;
			}
		}


		$td = $this->Html->tag('td', $title);
	}

	public function threshold($item)
	{
		$title = $item['DashboardKpi']['title'];
		if (empty($item['DashboardKpiThreshold'])) {
			return $title;
		}

		$value = $item['DashboardKpi']['value'];

		$finalColor = null;
		$finalTitle = null;
		foreach ($item['DashboardKpiThreshold'] as $threshold) {
			$min = $threshold['min'];
			$max = $threshold['max'];
			$percentage = $threshold['percentage'];
			$color = $threshold['color'];

			$rangeConds = $threshold['type'] == DashboardKpiThreshold::TYPE_RANGE;
			$rangeConds &= $value > $min && $value < $max;
			if ($rangeConds) {
				$finalColor = $color;
			}
		}

		return $this->Html->div('label bs-popover', $title, [
			'style' => 'background-color:' . $finalColor,
			'data-title' => 'Threshold Title',
			'data-content' => 'Threshold Description blablabla',
			'data-container' => 'body',
			'data-trigger' => 'hover',
			'data-placement' => 'top'
		]);
		// debug($item);exit;
	}

	protected function calculateThreshold()
	{

	}

	/**
	 * Renders a box with KPIs.
	 */
	public function widget($section, $model, $categories) {
		$widget = $this->_View->element('Dashboard.' . $section, [
			'model' => $model,
			'categories' => $categories
		]);

		return $this->Html->div('panel panel-flat', $widget, [
			'escape' => false
		]);
	}

	/**
	 * Shows the KPI value on the dashboard, otherwise if value is missing, it shows only a tooltip with information.
	 */
	public function getKpiValue($item) {
		$value = $item['DashboardKpi']['value'];

		if ($value !== null) {
			$ret = $value;
		}
		else {
			$ret = $this->noValueTooltip();
		}

		return $ret;
	}

	/**
	 * In case there is no value for a KPI, show only an informational tooltip.
	 * 
	 * @return string
	 */
	public function noValueTooltip() {
		return $this->Eramba->getTruncatedTooltip('', [
			'content' => __('Value will be recalculated during the next hourly CRON run')
		]);
	}

	public function getKpiLink($model, $item) {
		$Model = ClassRegistry::init($model);
		$value = $item['DashboardKpi']['value'];
		$valueIcon = $value;
		// $valueIcon = $value .  ' &nbsp;' . $this->Ux->getIcon('external-link') . '';

		// handler for saved filters as KPIs
		if (isset($item['attributes']['AdvancedFilter'])) {
			$link = $this->AdvancedFilters->getFilterRedirectUrl($item['attributes']['AdvancedFilter']);

			return $this->Html->link($value, $link);
		}

		// special handler for awareness program KPIs
		if (isset($item['attributes']['AwarenessProgram'])) {
			// debug($item);
			$combinedModel = 'AwarenessProgram' . $item['attributes']['AwarenessProgramUserModel'];

			$route = ClassRegistry::init($combinedModel)->getMappedRoute();
			$link = $this->AdvancedFilters->getItemFilteredLink(
				$valueIcon,
				$route['controller'],
				$item['attributes']['AwarenessProgram'],
				[
					'key' => 'awareness_program_id',
					// 'param' => $item['attributes']['AwarenessProgramUserModel']
				]
			);

			return $link;
		}

		$query = [];
		$findOn = $Model;
		foreach ($item['attributes'] as $attributeClass => $attribute) {
			$AttributeInstance = $this->DashboardKpi->instance()->attributeInstance($attributeClass);

			// $filterField = $AttributeInstance->mapFilterField($Model, $attribute);
			$query = $AttributeInstance->buildUrl($Model, $query, $attribute, $item);
		}

		$element = $this->AdvancedFilters->getItemFilteredLink($valueIcon, $findOn->alias, null, [
			'controller' => $findOn->getMappedController(),
			'query' => $query
		]);
			
		return $element;
	}

	/**
	 * Helper method that shows attributes of a KPI.
	 * 
	 * @param  string   Model class name.
	 * @return mixed    Array of nicely formatted attributes or boolean false if nothing found.
	 */
	public function getModelAttributeList($model) {
		$attributeList = $this->_View->get('attributeList');
		if ($attributeList !== null && isset($attributeList[$model])) {
			return $attributeList[$model];
		}

		return false;
	}

	public function displayModelLabel($model) {
		return ClassRegistry::init($model)->label();
	}

	// KPIs searched for in array of KPIs by its attributes
	public function searchKpiByAttributes(&$items, $attributes) {
		// debug($attributes);
		// dd($items);
		foreach ($items as $key => $item) {
			$found = true;
			foreach ($attributes as $attr => $value) {
				if (!isset($item['attributes'][$attr]) || $item['attributes'][$attr] != $value) {
					// debug($value);
					// dd($item);
					$found = false;
					break;
				}

			}

			if ($found) {
				$return = $item;
				// unset($items[$key]);

				return $return;
			}
		}
		// debug($items);
// dd($attributes);
		return false;
	}

}