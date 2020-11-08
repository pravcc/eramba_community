<?php
App::uses('TooltipsAppModel', 'Tooltips.Model');

class TooltipLog extends TooltipsAppModel
{
	const TYPE_SMALL = 'small';
	const TYPE_LARGE = 'large';

	public $belongsTo = [
		'User'
	];

	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
	}
}