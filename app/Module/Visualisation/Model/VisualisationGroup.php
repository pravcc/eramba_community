<?php
App::uses('Group', 'Model');

class VisualisationGroup extends Group {
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}

	public function bindNode($group) {
		$name = key($group);
		list(, $alias) = pluginSplit($name);

		return [
			'model' => 'Group',
			'foreign_key' => $group[$name]['id']
		];
    }

    public function parentNode($type) {
		return parent::parentNode();
	}
}
