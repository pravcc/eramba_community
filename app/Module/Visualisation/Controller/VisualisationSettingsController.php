<?php
/**
 * @package       Visualisation.Controller
 */
 
App::uses('VisualisationAppController', 'Visualisation.Controller');
App::uses('AppModule', 'Lib');

class VisualisationSettingsController extends VisualisationAppController {
	public $helpers = array('Html', 'Form', 'UserFields.UserField');
	public $components = array('Session','Search.Prg', 'Paginator', 'AppAcl', 
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'enabled' => true,
					'className' => 'AppIndex',
					'viewVar' => 'data'
				],
				'edit' => [
					'className' => 'AppEdit',
					'enabled' => true
				]
			]
		],
		'UserFields.UserFields' => [
			'fields' => ['ExemptedUser']
		]
	);
	public $uses = array(
		'Visualisation.VisualisationSetting'
	);

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function _beforePaginate(CakeEvent $e) {
		$settings = &$e->subject->controller->Paginator->settings;
		$settings['order'] = am(['VisualisationSetting.order' => 'ASC'], $settings['order']);
		$settings['limit'] = 100;

		// lets skip CompliancePackage and other irrelevant models for now
		$settings['conditions']['VisualisationSetting.model !='] = [
			'CompliancePackage',
			'DashboardCalendarEvent',
			'AppNotification'
		];
	}

	public function index() {
		// lets do a quick ACL check on settings in general

		$this->Crud->addListener('SettingsSubSection', 'SettingsSubSection');

		$this->set('title_for_layout', __('Visualisation Settings'));
		$this->set('subtitle_for_layout', __('Use visualisations to control what is displayed to the logged user. By default they are enabled, meaning users will only see items that relate to them.'));
		
		$this->Crud->on('beforePaginate', array($this, '_beforePaginate'));
		$this->Crud->execute();
	}

	public function _beforeSave(CakeEvent $e)
	{
		$id = $e->subject->id;
		$data = $e->subject->model->find('first', [
			'conditions' => [
				'VisualisationSetting.id' => $id
			],
			'fields' => [
				'VisualisationSetting.model'
			],
			'recursive' => -1
		]);

		$this->request->data['VisualisationSetting']['model'] = $data['VisualisationSetting']['model'];
	}

	public function edit($model) {
		$data = $this->VisualisationSetting->getItem($model);

		$this->Crud->on('beforeSave', [$this, '_beforeSave']);
		return $this->Crud->execute(null, [$data['VisualisationSetting']['id']]);

		// lets do a quick ACL check on settings in general
		/*$this->checkSettingsAccess();
		
		$this->set('title_for_layout', __('Visualisation Settings'));
		
		$data = $this->VisualisationSetting->getItem($model);

		if (empty($data)) {
			throw new NotFoundException();
		}

		$this->set('edit', true);
		$this->set('model', $model);
		$this->initOptions();

		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['VisualisationSetting']['id'] = $data['VisualisationSetting']['id'];
			$this->VisualisationSetting->set($this->request->data);

			if ($this->VisualisationSetting->validates()) {
				$dataSource = $this->VisualisationSetting->getDataSource();
				$dataSource->begin();

				$ret = $this->VisualisationSetting->save();
				if ($ret) {
					$this->Ajax->success();
					
					$dataSource->commit();
					$this->Session->setFlash(__('Visualisation Settings for this section was successfully edited.'), FLASH_OK);
				}
				else {
					$dataSource->rollback();
					$this->Session->setFlash(__('Error while saving the data. Please try it again.'), FLASH_ERROR);
				}
			} else {
				$this->Session->setFlash(__('One or more inputs you entered are invalid. Please try again.'), FLASH_ERROR);
			}
		}
		else {
			$this->request->data = $data;
		}

		$this->render('add');*/
	}

	protected function initOptions() {
		$FieldDataCollection = $this->VisualisationSetting->getFieldDataEntity();

		$this->set('FieldDataCollection', $FieldDataCollection);
		$this->set($FieldDataCollection->getViewOptions());
	}

	public function sync() {
		$this->loadModel('Setting');

		if ($this->Setting->syncVisualisation()) {
			$this->Flash->set(__('Visualisation synchronization successful'));
		}
		else {
			$this->Flash->error(__('Error occured during synchronization. Please try again.'));
		}

		return $this->redirect([
			'plugin' => 'visualisation',
			'controller' => 'visualisationSettings',
			'action' => 'index'
		]);
	}

}
