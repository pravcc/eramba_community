<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');

/**
 * FieldData Listener
 */
class FieldDataListener extends CrudListener
{

	public function implementedEvents() {
		return array(
			// 'Crud.beforeRender' => array('callable' => 'beforeRender', 'priority' => 50),
			'Crud.beforeFilter' => array('callable' => 'beforeFilter', 'priority' => 50),
			// 'AdvancedFilter.afterFind' => array('callable' => 'afterFilterFind', 'priority' => 50)
		);
	}

	/**
	 * Before filter action that changes things to apply trash.
	 */
	public function beforeFilter(CakeEvent $e)
	{
		// and attach an event to the advanced filter object to additionally make changes to the final query
		$AdvancedFiltersObject = $e->subject->AdvancedFiltersObject;
		$this->attachListener($AdvancedFiltersObject);
	}

	public function attachListener(AdvancedFiltersObject $Filter)
	{
		$Filter->getEventManager()->attach($this);
	}

	public function afterFilterFind(CakeEvent $e)
	{
		// $model = $this->_model();

		// $Collection = new ItemDataCollection($model);
		// foreach ($e->data[0] as $item) {
		// 	$Collection->add($item);
		// }

		// $e->data[0] = $Collection;
	}
	
	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeRender(CakeEvent $e)
	{
	}

}
