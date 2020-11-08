<?php
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');

class AdvancedFilterValue extends AppModel {
	public $actsAs = array(
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'field', 'value'
			)
		)
	);

}