<?php
App::uses('CrudListener', 'Crud.Controller/Crud');

class EditedColumnListener extends CrudListener
{

	public function implementedEvents() {
		return array(
			'Crud.beforeSave' => array('callable' => 'beforeSave', 'priority' => 50),
		);
	}

	/**
	 * Lets modify the request data only for EditCrudAction and fill in `edited` field value.\
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeSave(CakeEvent $e)
	{
		$subject = $e->subject;
		$model = $subject->model;
		$request = $subject->request;
		$action = $e->subject->crud->action();

		if ($action instanceof EditCrudAction && $model->hasField('edited')) {
			$time = call_user_func('date', 'Y-m-d H:i:s');
			$request->data[$model->alias]['edited'] = $time;
		}
	}

}
