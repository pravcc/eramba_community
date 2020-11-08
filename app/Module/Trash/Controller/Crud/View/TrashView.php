<?php
App::uses('ClassRegistry', 'Utility');
App::uses('CrudView', 'Controller/Crud');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');

class TrashView extends CrudView {

	/**
	 * Count of deleted objects within a model.
	 * 
	 * @var integer
	 */
	protected $_count;

	/**
	 * Initialize callback logic that sets the trash counter.
	 * 
	 * @return void
	 */
	public function initialize()
	{
		parent::initialize();

		if ($this->isEnabled()) {
			$this->_setCount();
		}
	}

	public function isEnabled()
	{
		return $this->getSubject()->model->Behaviors->loaded('Utils.SoftDelete');
	}

	/**
	 * Find and set count of deleted objects.
	 */
	protected function _setCount()
	{
		$cacheKey = 'toolbar_count_' . $this->getSubject()->model->alias . '_user_' . AuthComponent::user('id');

		if (($count = Cache::read($cacheKey, 'trash_settings')) === false) {
			$Filter = $this->_listener('Trash')->initTrashFilter();

			// lets attach all listeners that changes output (this should be made more dry)
			// check if VisualisationListener is used in current Crud
			if ($this->_crud()->config('listeners.Visualisation') !== null) {
				$this->_listener('Visualisation')->attachListener($Filter);
			}

			$this->_listener('Trash')->attachListener($Filter);

			$count = $Filter->filter('count');
			unset($Filter);

			Cache::write($cacheKey, $count, 'trash_settings');
		}
		
		$this->_count = $count;
	}

	public function hasItems()
	{
		return $this->_count > 0;
	}

	/**
	 * Get the count of deleted objects.
	 * 
	 * @return int
	 */
	public function getCount()
	{
		return $this->_count;
	}

	/**
	 * Determine if current action is a Trash action.
	 * 
	 * @return boolean True if its a Trash action, False otherwise.
	 */
	public function isTrash()
	{
		return $this->_listener('Trash')->isTrash();
	}

}
