<?php
/**
 * @package       Workflows.Controller
 */
 
App::uses('WorkflowsAppController', 'Workflows.Controller');

class WorkflowSettingsController extends WorkflowsAppController {
	public $helpers = array('Html', 'Form', 'Ajax' => array('controller' => 'workflowSettings'));
	public $components = array('Session', 'Paginator', 'Ajax' => array(
		'actions' => array('edit')
	));
	public $uses = array('Workflows.WorkflowSetting', 'Workflows.WorkflowStage');

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function edit($id, $model = null) {
		$data = $this->WorkflowSetting->getById($id, $model);

		if (empty($data)) {
			throw new NotFoundException();
		}

		$this->set('edit', true);
		$this->set('id', $id);
		$this->initOptions();

		if ($this->request->is('post') || $this->request->is('put')) {

			$this->request->data['WorkflowSetting']['id'] = $data['WorkflowSetting']['id'];
			$this->WorkflowSetting->set($this->request->data);

			if ($this->WorkflowSetting->validates()) {
				$dataSource = $this->WorkflowSetting->getDataSource();
				$dataSource->begin();

				$ret = $this->WorkflowSetting->save();

				if ($ret) {
					$dataSource->commit();
					$this->Session->setFlash(__('Workflow Settings for this section was successfully edited.'), FLASH_OK);
				}
				else {
					$dataSource->rollback();
					$this->Session->setFlash(__('Error while saving the data. Please try it again.'), FLASH_ERROR);
				}
			} else {
				$this->Session->setFlash(__('One or more inputs you entered are invalid. Please try again.'), FLASH_ERROR);
			}
		}
		// debug($this->request->data);
		// $data = $this->WorkflowSetting->getItem($model);
		// $this->request->data = $data;

		return $this->Crud->execute();
	}

	protected function initOptions() {
		$FieldDataCollection = $this->WorkflowSetting->getFieldDataEntity();

		$this->set('FieldDataCollection', $FieldDataCollection);
		$this->set($FieldDataCollection->getViewOptions());
	}
}
