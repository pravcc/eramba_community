<?php
App::uses('ConcurrentEditAppController', 'ConcurrentEdit.Controller');

class ConcurrentEditController extends ConcurrentEditAppController
{
	public $uses = [];
    public $components = [];

    public function echo($model, $foreignKey)
    {
    	$this->loadModel('ConcurrentEdit.ConcurrentEdit');
    	$this->ConcurrentEdit->setRecord($model, intval($foreignKey), $this->Auth->user('id'));

    	$this->render(false);
    }
}
?>