<?php
App::uses('Project', 'Model');

// base class for implementing attributes for a dashboard object
abstract class DashboardAttribute {
	public $Dashboard = null;
	public $DashboardKpiObject = null;

	// Toggle softDelete for this attribute
	public $softDelete = true;

	// holds the field mapping to get the filters to work using the new convention in field names
	public $mapFilterField = null;
	
	/**
	 * Empty constructor
	 */
	public function __construct(Dashboard $Dashboard, DashboardKpiObject $DashboardKpiObject = null) {
		$this->Dashboard = $Dashboard;
		$this->DashboardKpiObject = $DashboardKpiObject;

		$this->mapFilterField = [
		];
	}

	/**
	 * Build the subquery that is to be implemented to the final query.
	 *
	 * @return array Query parameters like conditions, joins,...
	 */
	abstract public function buildQuery(Model $Model, $attribute);

	/**
	 * Builds the part of the query for url that manages to filter using a given attribute.
	 * 
	 * @param  Model  $Model     Model.
	 * @param  array  &$query    Query that hold parameters for URL.
	 * @param  string $attribute Attribute value.
	 * @param  array  $item      The entire item data.
	 * @return array             Returns the modified $query.
	 */
	public function buildUrl(Model $Model, &$query, $attribute, $item = []) {
		return $query;
	}
	/**
	 * Maps the field used for calculating the KPI to the actual field that can be used in filters.
	 */
	public function mapFilterField(Model $Model, $field) {
		if (isset($this->mapFilterField[$Model->alias][$field])) {
			return $this->mapFilterField[$Model->alias][$field];
		}

		return $field;
	}

	public function softDelete(Model $Model, $attribute) {
		return $this->softDelete;
	}

	public function getLabel(Model $Model, $attribute) {
		return null;
	}
}
