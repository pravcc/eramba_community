<?php
App::uses('View', 'View');

class AdvancedFiltersData {
	const VIEW_TYPE_HTML = 'html';
	const VIEW_TYPE_CSV = 'csv';

	protected $_viewData = null;
	protected $_viewType = null;

	public function __construct($data = array(), $type = self::VIEW_TYPE_HTML) {
		$this->setViewData($data);
		$this->_viewType = $type;

		return $this;
	}

	public function setViewData($data) {
		$this->_viewData = $data;
	}

	public function getViewType() {
		return $this->_viewType;
	}

	public function getViewValue($field) {
		if (empty($this->_viewData)) {
			return false;
		}

		return $this->_viewData[$field];
	}

	public function getFieldValue($filter, $field, $fieldData, $item) {
		$htmlHelper = 'Html';
		$htmlHelperClass = $htmlHelper . 'Helper';

		App::import('Helper', $htmlHelper);
		$htmlClass = new $htmlHelperClass(new View());

		// lets set the default value
		$value = false;
		if (!empty($fieldData['filter']['status'])) {
			$baseHelper = Inflector::pluralize($filter['model']);
			$baseHelperClass = $baseHelper . 'Helper';

			App::import('Helper', $baseHelper);
			$baseClass = new $baseHelperClass(new View());

			$value = $baseClass->getFilterSingleStatus(
				$item,
				$fieldData['filter']['status']['model'],
				array($fieldData['filter']['status']['field'])
			);

			// temporary fix for statuses that will be replaced in a near future because they are old dated
			if (isset($fieldData['data']['method']) && $fieldData['data']['method'] == 'getStatusFilterOptionInverted') {
				$value = !$value;
			}
		}
		elseif (!empty($fieldData['filter']['customField'])) {
			$customFieldsHelper = 'CustomFields';
			$customFieldsHelperClass = 'CustomFieldsHelper';

			App::uses('CustomFieldsHelper', 'CustomFields.View/Helper');
			
			$customFieldsClass = new $customFieldsHelperClass(new View());

			$value = $customFieldsClass->getItemValue($item, $fieldData['filter']['customField']);
		}
		elseif (!empty($fieldData['field'])) {
			if ($fieldData['field'] == 'all') {
				$value = $item;
			}
			else {
				$extract = Hash::extract($item, $fieldData['field']);
				if (!empty($fieldData['filter']['userField'])) {
					$user = ".{$fieldData['filter']['userField']}.";
					$group = ".{$fieldData['filter']['userField']}Group.";
					$groupField = str_replace($user, $group, $fieldData['field']);
					$extract = array_merge($extract, Hash::extract($item, $groupField));
				}

				//case when field is special function
				if ($extract === array() && isset($item[0][$field])) {
					$extract = array($item[0][$field]);
				}
				$value = array_unique($extract, SORT_REGULAR);
				if (empty($fieldData['many'])) {
					if (isset($value[0]) && (!empty($value[0]) || !empty($fieldData['data']['result_key']))) {
						$value = $this->wrapValue($value[0], $fieldData, $htmlClass);
					}
					else {
						$value = '-';
					}
				}	
			}
			
		}
		elseif (empty($fieldData['contain'])) {
			if (isset($item[$filter['model']][$field])) {
				$value = $this->wrapValue($item[$filter['model']][$field], $fieldData, $htmlClass);
			}
		}
		elseif (!empty($fieldData['many'])) {
			$value = array();
			foreach ($fieldData['contain'] as $alias => $aliasFields) {
				foreach ($item[$alias] as $subItem) {
					$valueItem = '';
					foreach ($aliasFields as $key => $aliasField) {
						if ($key > 0) {
							$valueItem .= ' ';
						}
						$valueItem .= isset($subItem[$aliasField]) ? $subItem[$aliasField] : '';
					}
					$value[] = $valueItem;
				}
			}
		}
		else {
			$value = '';
			foreach ($fieldData['contain'] as $alias => $aliasFields) {
				foreach ($aliasFields as $key => $aliasField) {
					if ($key > 0) {
						$value .= ' ';
					}
					$value .= $item[$alias][$aliasField];
				}
			}
		}

		// in case we got a customized output filter set
		if ($this->hasOutputFilter($fieldData)) {
			$displayValue = $this->processOutputFilter($value, array(
				'AdvancedFiltersDataInstance' => $this,
				'fieldData' => $fieldData,
				'field' => $field
			));

			// if $displayValue is not false we return the value, otherwise we continue with ordinary execution.
			if ($displayValue !== false) {
				return $displayValue;
			}
		}

		// we set the final contents of a table cell into $displayValue before echo-ing it
		$displayValue = '';
		if ($fieldData['type'] == 'date') {
			// for now we wont format dates
			// echo date('d.m.Y', strtotime($value));
			if (!empty($fieldData['many'])) {
				foreach ($value as $key => $valueItem) {
					$formatedManyValue = $this->wrapValue($valueItem, $fieldData, $htmlClass);
					if ($this->getViewType() == self::VIEW_TYPE_HTML) {
						$formatedManyValue = $htmlClass->tag('span', $formatedManyValue, array('class' => 'label label-improvement'));
					}
					$manyValue[] = $formatedManyValue;
				}
				$displayValue = ($this->getViewType() == self::VIEW_TYPE_HTML) ? implode(' ', $manyValue) : ImportToolModule::buildValues($manyValue);
			}
			else {
				$displayValue = $this->wrapValue($value, $fieldData, $htmlClass);
			}
		}
		elseif (!empty($fieldData['many'])) {
			$manyValue = array();
			foreach ($value as $key => $valueItem) {
				if (!empty($fieldData['data']['result_key'])) {
					$valueItem = $this->applyLableOnKey($field, $valueItem);
				}

				$formatedManyValue = $valueItem;
				if ($this->getViewType() == self::VIEW_TYPE_HTML) {
					$formatedManyValue = $htmlClass->tag('span', $valueItem, array('class' => 'label label-improvement'));
				}

				$manyValue[] = $formatedManyValue;
			}

			$displayValue = ($this->getViewType() == self::VIEW_TYPE_HTML) ? implode(' ', $manyValue) : ImportToolModule::buildValues($manyValue);
		}
		else {
			if (!empty($fieldData['data']['result_key'])) {
				$displayValue = $this->applyLableOnKey($field, $value);
			}
			else {
				$displayValue = $value;
			}
		}

		return $displayValue;
	}

	/**
	 * add nowrap span wrapper on date values to prevent line break
	 */
	protected function wrapValue($value, $fieldData, $htmlClass) {
		if ($fieldData['type'] == 'date' && $this->getViewType() != self::VIEW_TYPE_CSV) {
			$value = $htmlClass->tag('span', $value, array('class' => 'text-nowrap'));
		}

		return $value;
	}

	/**
	 * Check if current filter field has customized output filter.
	 */
	protected function hasOutputFilter($fieldData = array()) {
		return !empty($fieldData['outputFilter']);
	}

	/**
	 * Process custom filter output. If function returns false, we continue with ordinary output execution.
	 */
	protected function processOutputFilter($data, $options = array()) {
		$fieldData = $options['fieldData'];
		if (!$this->hasOutputFilter($fieldData)) {
			return false;
		}

		$helper = $fieldData['outputFilter'][0];
		$method = $fieldData['outputFilter'][1];

		$outputHelper = $helper;
		$outputHelperClass = $outputHelper . 'Helper';

		App::import('Helper', $outputHelper);
		$outputClass = new $outputHelperClass(new View());

		return call_user_func_array(array($outputClass, $method), array($data, $options));
	}

	public function applyLableOnKey($field, $value) {
		$displayValue = '';
		$optionsVar = $field . '_data';
		// $options = $$optionsVar;

		$options = $this->getViewValue($optionsVar);

		if (isset($options[$value])) {
			$displayValue = $options[$value];
		}

		return $displayValue;
	}

}