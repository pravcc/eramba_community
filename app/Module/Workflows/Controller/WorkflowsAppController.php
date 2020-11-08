<?php
/**
 * @package       Workflows.Controller
 */

App::uses('AppController', 'Controller');

class WorkflowsAppController extends AppController {
	public $components = array(
		'Session', 'Paginator',
		'Crud.Crud' => [
			'actions' => [
				// The controller action 'index' will map to the IndexCrudAction
				'index' => [
					'className' => 'Crud.Index',
					'viewVar' => 'data'
				],
				// The controller action 'add' will map to the AddCrudAction
				'add' => [
					'className' => 'AppAdd',
				],
				// The controller action 'edit' will map to the EditCrudAction
				'edit' => [
					'className' => 'AppEdit',
				],
				// The controller action 'view' will map to the ViewCrudAction
				//'view'  => 'Crud.View'
				// The controller action 'delete' will map to the DeleteCrudAction
				'delete' => [
					'className' => 'AppDelete',
				]
			],
		]
	);

	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->set('use_new_breadcrumbs', true);
	}

	protected function getSubtitle($model, $foreignKey) {
		$this->loadModel($model);
		$title = $this->{$model}->getRecordTitle($foreignKey);

		return sprintf(
			'%s, %s',
			$this->getLabel($model),
			$title
		);
	}

	protected function getObjectTitle($InstanceClass, $model) {
		return $InstanceClass->Object->{$this->getDisplayField($model)};
	}

	protected function getDisplayField($model) {
		$this->loadModel($model);
		return $this->{$model}->displayField;
	}

	protected function getLabel($model) {
		$this->loadModel($model);
		return $this->{$model}->label();
	}

	protected function getFormattedObjectTitle($InstanceClass, $model) {
		return sprintf(
			'%s, %s',
			$this->getLabel($model),
			$this->getObjectTitle($InstanceClass, $model)
		);
	}
}
