<?php
/**
 * @package       Workflows.Controller
 */
 
App::uses('VisualisationAppController', 'Visualisation.Controller');
App::uses('AppModule', 'Lib');
App::uses('Hash', 'Utility');

class VisualisationController extends VisualisationAppController {
	public $helpers = array('Html', 'Form', 'Ajax' => array('controller' => 'visualisation'));
	public $components = array('Session',
		'Crud.Crud' => [
			'actions' => [
				'share' => [
					'enabled' => true,
					'className' => 'AppEdit'
				]
			]
		]
	);

	public $uses = array(
		'Visualisation.VisualisationShare'
	);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('redirectGateway');
	}

	// gateway to redirect users to sections based on visualisation shared object.
	public function redirectGateway($model, $id) {
		$M = ClassRegistry::init('Visualisation.' . $model);

		$shareData = $M->getShareData($id);
		$object = $M->getSharedObject($shareData[$M->alias]['aros_acos_id']);

		$Model = ClassRegistry::init($object['Aco']['model']);

		$url = $Model->getSectionIndexUrl();

		return $this->redirect($url);
	}

	public function share($model, $foreignKey)
	{
		$data = $this->VisualisationShare->getItem($model, $foreignKey);

		$this->Crud->on('beforeSave', [$this, '_beforeSave']);
		return $this->Crud->execute(null, [$data['VisualisationShare']['id']]);
	}

	public function _beforeSave(CakeEvent $e)
	{
		$model = $e->subject->model;
		$request = $e->subject->request;
		$id = $e->subject->id;

		$data = $model->find('first', [
			'conditions' => [
				'VisualisationShare.id' => $id
			],
			'fields' => [
				'VisualisationShare.model',
				'VisualisationShare.foreign_key'
			],
			'recursive' => -1
		]);

		$request->data['VisualisationShare']['model'] = $data['VisualisationShare']['model'];
		$request->data['VisualisationShare']['foreign_key'] = $data['VisualisationShare']['foreign_key'];
	}

	/**
	 * Visualisation action to share an object to other users/groups and set correct permissions for the ACL.
	 * 
	 * @param  string $model      Model name
	 * @param  int    $foreignKey Foreign key of the object
	 * @return void
	 */
	public function share_old2($model, $foreignKey) {
		$this->set('title_for_layout', __('Visualisation Options'));
		$this->set('showHeader', true);
		$this->set('modalPadding', true);
		
		$data = $this->VisualisationShare->getItem($model, $foreignKey);
		if (empty($data)) {
			throw new NotFoundException();
		}

		$this->set('edit', true);
		$this->set('model', $model);
		$this->set('foreign_key', $foreignKey);
		$this->initOptions($model, $foreignKey);

		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['VisualisationShare']['id'] = $data['VisualisationShare']['id'];
			$this->VisualisationShare->set($this->request->data);

			if ($this->VisualisationShare->validates()) {
				$dataSource = $this->VisualisationShare->getDataSource();
				$dataSource->begin();

				$ret = $this->VisualisationShare->save();
				if ($ret) {
					$this->Ajax->success();
					
					$dataSource->commit();
					$this->Session->setFlash(__('Item has been successfully shared.'), FLASH_OK);
				}
				else {
					$dataSource->rollback();
					$this->Session->setFlash(__('Error occured, we are unable to complete your request at the moment. Please try again.'), FLASH_ERROR);
				}
			} else {
				$this->Session->setFlash(__('One or more inputs you entered are invalid. Please try again.'), FLASH_ERROR);
			}
		}
		else {
			$this->request->data = $data;
		}
	}

	/**
	 * tbd
	 */
	public function share_old($model, $foreignKey) {
		$this->set('title_for_layout', __('Visualisation Options'));
		$this->set('showHeader', true);
		$this->set('modalPadding', true);

		$this->initOptions($model, $foreignKey);

		$this->set([
			'model' => $model,
			'foreignKey' => $foreignKey
		]);
		$exists = $this->VisualisationShare->findExisting($model, $foreignKey);
		$existsExtracted = Hash::extract($exists, '{n}.VisualisationShare.user_id');
		$this->set('existsExtracted', $existsExtracted);

		if ($this->request->is('post')) {
			$dataSource = $this->VisualisationShare->getDataSource();
			$dataSource->begin();

			$selectedUsers = $this->request->data['VisualisationShare']['user_id'];
			if (empty($selectedUsers)) {
				$selectedUsers = [];
			}

			$newUsers = array_diff($selectedUsers, $existsExtracted);
			$removedUsers = array_diff($existsExtracted, $selectedUsers);
			$noChangeUsers = array_intersect($existsExtracted, $selectedUsers);

			$ret = true;
			foreach ($removedUsers as $userId) {
				$ret &= $this->VisualisationShare->unshare($userId, [$model, $foreignKey]);
			}

			foreach ($newUsers as $userId) {
				$ret &= $this->VisualisationShare->share($userId, [$model, $foreignKey], true);
			}

			if ($ret) {
				$dataSource->commit();
				$this->Ajax->success();	
				$this->Session->setFlash(__('Item has been successfully shared.'), FLASH_OK);
			}
			else {
				// $validationError = array_values($this->VisualisationShare->requestErrors);

				$msg = __('Error occured, we are unable to complete your request at the moment. Please try again.');
				if (isset($validationError[0][0])) {
					$msg = $validationError[0][0];
				}
				
				$dataSource->rollback();
				$this->Session->setFlash($msg, FLASH_ERROR);
			}
		}
		else {
			// $this->request->data['user_id'] = $existsExtracted;
		}
		

	}

	/**
	 * Options for share.
	 */
	protected function initOptions($model, $foreignKey) {
		$FieldDataCollection = $this->VisualisationShare->getFieldDataEntity();
		// debug($FieldDataCollection->getViewOptions());
		$this->set($FieldDataCollection->getViewOptions());
	}

}