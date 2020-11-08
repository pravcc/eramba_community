<?php
App::uses('CrudAction', 'Crud.Controller/Crud');
App::uses('AdvancedFiltersComponent', 'Controller/Component');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');

/**
 * Handles 'Filter' Crud actions
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 */
class FilterCrudAction extends CrudAction {
	use CrudActionTrait;

	const ACTION_SCOPE = CrudAction::SCOPE_MODEL;
	
	protected $_settings = array(
		'enabled' => true,
		'filterType' => AdvancedFiltersComponent::FILTER_TYPE_INDEX
	);

	protected function _handle() {
		if (!$this->hasAdvancedFilters()) {
            return false;
        }
		
		$response = $this->_controller()->AdvancedFilters->filter(
			$this->_model()->alias,
			'html',
			$this->config('filterType')
		);

		return $response;

	}

}