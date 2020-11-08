<?php
App::uses('AppHelper', 'View/Helper');

class BulkActionsCrudHelper extends AppHelper {
	public $helpers = ['Html'];

	public function beforeRender($viewFile)
	{
		$BulkActions = $this->_View->get('BulkActions');
	}
}
