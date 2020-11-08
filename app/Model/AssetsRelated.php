<?php
class AssetsRelated extends AppModel {
	public $useTable = 'assets_related';

	public function __construct($id = false, $table = null, $ds = null) {
		unset($this->actsAs['ReviewsPlanner.Reviews']);

		parent::__construct($id, $table, $ds);
	}
}
