<?php
App::uses('AppHelper', 'View/Helper');

class InlineEditCrudHelper extends AppHelper {
	public $helpers = ['Html'];

	public function beforeRender($viewFile)
	{
		$InlineEdit = $this->_View->get('InlineEdit');
	}
}
