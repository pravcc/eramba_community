<?php
App::uses('CrudView', 'Controller/Crud');
App::uses('ObjectStatusHelper', 'ObjectStatus.View/Helper');

class ObjectStatusView extends CrudView
{
	public function isEnabled()
	{
		return $this->getSubject()->model->Behaviors->loaded('ObjectStatus.ObjectStatus');
	}

	public function isShowable()
	{
		return ObjectStatusHelper::isShowable($this->getSubject()->model);
	}
}
