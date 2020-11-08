<?php
App::uses('ImportToolAppController', 'ImportTool.Controller');
App::uses('ImportTool', 'ImportTool.Model');
App::uses('ImportToolCsv', 'ImportTool.Lib');
App::uses('ImportToolData', 'ImportTool.Lib');
App::uses('ImportToolImport', 'ImportTool.Lib');
App::uses('ImportToolListener', 'ImportTool.Controller/Crud/Listener');

class ImportToolController extends ImportToolAppController
{
	public $helpers = [];
	public $components = [
		'Crud.Crud' => [
			'actions' => [
				'upload' => [
					'className' => 'AppAdd',
					'view' => 'ImportTool.ImportTool/upload',
					'saveMethod' => 'storeImportToolData',
					'messages' => [
						'success' => [
							'text' => 'File for import successfully uploaded',
						]
					]
				],
				'preview' => [
					'className' => 'AppAdd',
					'view' => 'ImportTool.ImportTool/preview',
					'saveMethod' => 'import',
					'messages' => [
						'success' => [
							'text' => 'Successfully imported',
						]
					]
				],
			],
		],
	];

	protected $_appControllerConfig = [
		'components' => [
		],
		'helpers' => [
		],
		'elements' => [
		]
	];

	public function beforeFilter()
	{
		$this->Crud->enable(['upload', 'preview']);

		parent::beforeFilter();
		ini_set("auto_detect_line_endings", true);

		$this->title = __('Import Tool');
		$this->subTitle = __('');
	}

	public function upload($model)
	{
		if (empty($model)) {
			throw new NotFoundException();
		}

		$this->title = __('Import %s', ClassRegistry::init($model)->label(['singular' => true]));

		$this->Crud->on('beforeSave', [$this, '_beforeUpload']);
		$this->Crud->on('afterSave', [$this, '_afterUpload']);
		$this->Crud->on('beforeRender', [$this, '_beforeRenderUpload']);

		$this->Crud->execute();
	}

	public function _beforeUpload(CakeEvent $event)
	{
		if (!empty($event->subject->request->params['pass'][0])) {
			$event->subject->request->data['ImportTool']['model'] = $event->subject->request->params['pass'][0];
		}
	}

	public function _afterUpload(CakeEvent $event)
	{
		$subject = $event->subject;
		$model = $subject->request->params['pass'][0];

		if ($subject->success) {
			// we redirect it to the add action of defined section having import tool parameter
			$sectionRoute = ClassRegistry::init($model)->getMappedRoute([
				'action' => 'add'
			]);

			$sectionRoute['?'] = [
				ImportToolListener::REQUEST_PARAM => true
			];

			$this->redirect($sectionRoute);
			// $this->redirect(['action' => 'preview']);
		}
		elseif (empty($subject->model->validationErrors)) {
			$this->Session->setFlash(__('Error occured while processing the file. Please try again.'), FLASH_ERROR);
		}
	}

	public function _beforeRenderUpload(CakeEvent $event)
	{
		$model = $event->subject->request->params['pass'][0];

		$this->Modals->setHeaderHeading($this->title);
		$this->Modals->changeConfig('footer.buttons.saveBtn.text', __('Submit'));
		$this->Modals->changeConfig('footer.buttons.saveBtn.options.data-yjs-on-modal-success', 'false');
		$this->Modals->changeConfig('footer.buttons.saveBtn.options.data-yjs-on-success-reload', 'none');

		$this->set('model', $model);
	}

	/**
	 * Preview page for the parsed file's data that user wants to import.
	 */
	public function preview()
	{
		$this->title = __('Preview Import Data');

		$ImportToolData = $this->ImportTool->getStoredImportToolData();

		if (empty($ImportToolData)) {
			$this->Session->setFlash(__('There is nothing to preview.'), FLASH_ERROR);
			throw new NotFoundException();
		}

		// $this->Crud->on('afterSave', [$this, '_afterImport']);
		// $this->Crud->on('beforeRender', [$this, '_beforeRenderPreview']);

		$this->set('ImportToolData', $ImportToolData);
		$ImportToolData = null;

		return $this->Crud->execute();
	}

	public function _afterImport(CakeEvent $event)
	{
		if (!empty($ImportTool->validationErrors['ImportToolData'][0])) {
			$this->Session->setFlash($ImportTool->validationErrors['ImportToolData'][0]);
		}
	}

	public function _beforeRenderPreview(CakeEvent $event)
	{
		$this->Modals->setHeaderHeading($this->title);
		$this->Modals->changeConfig('footer.buttons.saveBtn.text', __('Import'));
		$this->Modals->changeConfig('footer.buttons.saveBtn.options.data-yjs-datasource-url', Router::url([
			'plugin' => null,
			'controller' => 'legals',
			'action' => 'add',
			'?' => [
				'ImportTool' => 1
			]
		]));
	}

	/**
	 * Let the user download a CSV taht includes fields necessary for import
	 */
	public function downloadTemplate($model, $getData = false)
	{
		if (empty($model)) {
			throw new NotFoundException();
		}

		App::uses('ImportToolTemplate', 'ImportTool.Lib');
		$Model = ClassRegistry::init($model);
		$templateClass = new ImportToolTemplate($Model);
		$arguments = $templateClass->getArguments();
		$argumentLabels = $templateClass->getArgumentLabels();

		if (empty($getData)) {
			$data = array($argumentLabels);

			$_serialize = 'data';

			$this->response->download(Inflector::slug($Model->label()) . '-import-template.csv');
			$this->viewClass = 'CsvView.Csv';
			$this->set(compact('data', '_serialize'));
		}
		else {
			$modelData = $Model->find('all');
			$data = $templateClass->convertDataToExport($modelData);

			$_serialize = 'data';
			$_header = false;

			$_bom = true;

			$this->response->download(Inflector::slug($Model->label()) . '-export.csv');
			$this->viewClass = 'CsvView.Csv';
			$this->set(compact('data', '_header', '_serialize'));
		}
	}
}