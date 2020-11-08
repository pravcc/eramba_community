<?php
App::uses('BulkActionsAppController', 'BulkActions.Controller');
App::uses('BulkAction', 'BulkActions.Model');

/**
 * BulkActions Controller
 */
class BulkActionsController extends BulkActionsAppController {

/**
 * Scaffold
 *
 * @var mixed
 */
	public $scaffold;

	public function beforeFilter() {
		parent::beforeFilter();

		$this->Security->csrfCheck = false;
	}

	public function apply() {
		$this->BulkAction->set($this->request->data);
		$valid = $this->BulkAction->validates();

		if ($valid) {
			// lets load the used model at the beginning
			$model = $this->request->data['BulkAction']['model'];
			$this->loadModel($model);

			$editableEntities = $this->BulkAction->getEditableEntities(
				$this->request->data['BulkAction']['model']
			);
			
			foreach ($editableEntities as $editable) {
				$this->set($editable->getViewOptions());
			}

			$this->set('model', $model);
			$this->set('type', $this->request->data['BulkAction']['type']);
			$this->set('editableEntities', $editableEntities);
		}
		else {
			$this->Session->setFlash(__('Select one or more objects and action to apply on them. Please try again.'), FLASH_ERROR);
		}

		$this->set('valid', $valid);
	}

	public function submit() {
		$data = $this->request->data;

		$this->BulkAction->set($data);
		$valid = $this->BulkAction->validates();

		// $success = false;
		if ($valid) {
			// lets load the used model at the beginning
			$model = $data['BulkAction']['model'];
			$this->loadModel($model);
			
			// additional relevant acl check
			$mappedController = $this->{$model}->getMappedController();
			$aclCheck = [
				'plugin' => (!empty($this->{$model}->plugin)) ? Inflector::underscore($this->{$model}->plugin) : null,
				'controller' => $mappedController,
				'action' => $data['BulkAction']['type'] == BulkAction::TYPE_EDIT ? 'edit' : 'delete'
			];

			if (!$this->AppAcl->check($aclCheck)) {
				throw new ForbiddenException(__('You are not allowed to apply a bulk action here.'));
			}
			
			$data = $this->BulkAction->processNoChanges($data, $model);

			$dataSource = $this->BulkAction->getDataSource();
			$dataSource->begin();

			$ret = true;
			if ($data['BulkAction']['type'] == BulkAction::TYPE_EDIT) {
				// we copy all of the submitted data 
				$modelData = $data;

				// we clean the copied data from BulkAction-related information,
				// and leave only relevant model's information, keeping it's format for further saving
				unset($modelData['BulkAction']);

				// we clean the BulkAction data from other model's information
				unset($data[$model]);

				foreach ($data['BulkAction']['apply_id'] as $foreignKey) {
					$modelData[$model]['id'] = $foreignKey;
					
					$this->{$model}->set($modelData);
					$this->{$model}->id = $foreignKey;
					
					$ret &= $this->{$model}->save($modelData, array(
						'atomic' => false,
						'validate' => true,
						'fieldList' => array_keys($modelData[$model])
					));

					if ($this->{$model}->Behaviors->enabled('ObjectStatus')) {
						$ret &= (boolean) $this->{$model}->triggerObjectStatus();
					}
					
					if (!$ret) {
						break;
					}
				}
			}

			if ($data['BulkAction']['type'] == BulkAction::TYPE_DELETE) {
				foreach ($data['BulkAction']['apply_id'] as $foreignKey) {
					$ret &= $this->{$model}->delete($foreignKey);

					if ($this->{$model}->Behaviors->enabled('ObjectStatus')) {
						$ret &= (boolean) $this->{$model}->deleteObjectStatus();
					}
					
					if (!$ret) {
						break;
					}
				}
			}

			$ret &= $this->BulkAction->saveAssociated($data, array(
				'validate' => 'first',
				'atomic' => false
			));
			
			if ($ret) {
				$valid = true;
				$dataSource->commit();
			}
			else {
				$valid = false;
				$dataSource->rollback();
			}
		}

		$this->set('submitAction', true);

		if (empty($valid)) {
			$this->set('valid', $valid);

			$this->apply();
			$this->render('apply');
		}
		else {
			$this->set('success', true);
			$this->Session->setFlash(__('Bulk action has been successfully applied.'), FLASH_OK);
		}
	}

}
