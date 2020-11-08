<?php
class ProjectAchievementsHelper extends AppHelper {
	public $helpers = array('Html', 'LimitlessTheme.Alerts', 'FieldData.FieldData', 'FormReload');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function projectField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, $this->FormReload->triggerOptions());
	}

	public function dateField(FieldDataEntity $Field)
	{
		$lastAchievement = $this->_View->viewVars['lastAchievement'];

		$out = $this->FieldData->input($Field, [
			'default' => $lastAchievement
		]);

		if ($lastAchievement !== false) {
			$out .= $this->Alerts->info(_('The last task on this project has a deadline set, we have included that on the field above. Most likely, you are looking at setting a deadline after that date.'));
		}

		return $out;
	}

	public function taskOrderField(FieldDataEntity $Field)
	{
		$order = $this->_View->viewVars['order'];
		$lastOrder = $this->_View->viewVars['lastOrder'];

		$defaultOrder = 0;
		if ($lastOrder !== false) {
			$defaultOrder = $lastOrder;
		}

		// highest number of $order variable is reset($order) = first value
		// because the array is flipped
		if ($defaultOrder != reset($order)){
			// next possible ordering
			$defaultOrder += 1;
		}

		$out = $this->FieldData->input($Field, [
			'options' => $order,
			'selected' => $this->request->params['action'] === 'add' ? $defaultOrder : null
		]);

		if ($lastOrder !== false) {
			$out .= $this->Alerts->info(
				__(
					'It seems the next task will be number %d (there is a task with order %d already and no task with number greater than that).',
					$defaultOrder,
					$lastOrder
				)
			);
		}

		return $out;
	}
}