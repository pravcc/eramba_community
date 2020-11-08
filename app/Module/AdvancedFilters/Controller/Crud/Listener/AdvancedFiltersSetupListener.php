<?php
App::uses('CrudListener', 'Crud.Controller/Crud');

/**
 * Advanced Filters first login user account setup Listener
 */
class AdvancedFiltersSetupListener extends CrudListener
{

	public function implementedEvents() {
		return array(
			'Crud.onFirstLogin' => 'onFirstLogin'
		);
	}
	
	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function onFirstLogin(CakeEvent $e)
	{
		$userId = $e->subject->id;

		// temporary solution to not duplicate filters in case of "account getting ready feature" fails
		$AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
		$countFilters = $AdvancedFilter->find('count', [
			'conditions' => [
				'AdvancedFilter.user_id' => $userId,
				'AdvancedFilter.model' => 'Legal'
			],
			'recursive' => -1
		]);
		
		$ret = true;
		if (!$countFilters) {
			// sync filters
			$ret = (bool) $AdvancedFilter->syncDefaultIndex($userId);
		}

		if (!$ret) {
			// stop propagation
			return false;
		}
	}

}
