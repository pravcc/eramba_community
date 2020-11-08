<?php
interface SectionInterface {
	public function setActionList($item);
	public function getActionList($item, $model);
	public function getStatusList($item);

	// public function getToolbarButtons();
}
