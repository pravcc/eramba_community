<?php
App::uses('SystemLogsController', 'SystemLogs.Controller');

class UserSystemLogsController extends SystemLogsController
{
	public $uses = ['UserSystemLog'];

	public function beforeFilter()
	{
		parent::beforeFilter();

		$this->title = __('User Audit Trails');
		$this->subTitle = __('');
	}
}