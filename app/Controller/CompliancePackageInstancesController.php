<?php
App::uses('AppController', 'Controller');

class CompliancePackageInstancesController extends AppController
{
	public function beforeFilter() {
		return $this->redirect(Router::url(['controller' => 'complianceManagements', 'action' => 'index']));
	}
}
