<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Router', 'Routing');

class LayoutHelper extends AppHelper
{
	public $helpers = ['Html', 'Icon', 'LimitlessTheme.LayoutToolbar'];

	public function __construct(View $View, $settings = array())
	{
		parent::__construct($View, $settings);

		$this->_init();
	}

	protected function _init()
	{
		
	}
}
