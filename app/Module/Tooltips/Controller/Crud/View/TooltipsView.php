<?php
App::uses('ClassRegistry', 'Utility');
App::uses('CrudView', 'Controller/Crud');

class TooltipsView extends CrudView
{
	/**
	 * If true, tooltips will be always displayed even on sections when user already saw it.
	 * This is for testing purposes only. In production version this needs to be set to "false"
	 * @var boolean
	 */
	protected $disableCheck = false;

	public function active()
	{
		// Temporary disable check ()
		// return true;

		if (!$this->disableCheck) {
			// Get data from database tooltips table and find out if actual logged user already saw this tooltip or not
			$TooltipLog = ClassRegistry::init('Tooltips.TooltipLog');
			$seen = $TooltipLog->find('first', [
				'conditions' => [
					'seen' => 1,
					'user_id' => $this->_controller()->Auth->user('id')
				]
			]);

			if (empty($seen)) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
}
