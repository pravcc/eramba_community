<?php
App::uses('AppHelper', 'View/Helper');
App::uses('LimitlessThemeException', 'Module/LimitlessTheme/Error');

class ModalsHelper extends AppHelper
{
	public $helpers = array('Html', 'Form');

	public function __construct(View $view, $settings = array())
	{
		parent::__construct($view, $settings);
	}
}
