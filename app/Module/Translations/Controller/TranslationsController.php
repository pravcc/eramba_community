<?php
App::uses('TranslationsAppController', 'Translations.Controller');
App::uses('Translation', 'Translation.Model');
App::uses('Router', 'Routing');
App::uses('CakeEvent', 'Event');

class TranslationsController extends TranslationsAppController
{
	public $helpers = [
		'Html', 'Form', 'Translations.TranslationsCrud'
	];

	public $components = [
		'Session', 'Paginator',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
			],
			'listeners' => [
				'Widget.Widget'
			]
		]
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
		parent::beforeFilter();

		$this->Crud->enable(['index', 'add', 'edit', 'delete']);
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		$this->title = __('Translations');
		$this->subTitle = __('Manage the languages that will be available for users, note that although most of the system can be translated certain parts of the system wont be translated.');

		return $this->Crud->execute();
	}

	public function add()
	{
		$this->title = __('Create a Translation');

		$this->Crud->on('afterSave', function(CakeEvent $event) {
			$this->Translation->clearCache();
		});

		return $this->Crud->execute();
	}

	public function edit($id = null)
	{
		$this->title = __('Edit a Translation');

		if ($id == Translation::DEFAULT_TRANSLATION_ID) {
			throw new NotFoundException();
		}

		$this->Crud->on('afterSave', function(CakeEvent $event) {
			if ($event->subject->success) {
				$this->Translation->clearCache();

				if (!$this->Translation->isTranslationAvailable($event->subject->id)) {
					$this->_unsetActiveTranslation($event->subject->id);
				}
			}
		});

		$this->Crud->on('beforeRender', function(CakeEvent $event) {
			$id = $this->request->params['pass'][0];

			if ($this->Translation->isSystemTranslation($id)) {
				$this->_FieldDataCollection->get('name')->config('editable', false);
				$this->_FieldDataCollection->get('file')->config('editable', false);
			}
		});

		return $this->Crud->execute();
	}

	protected function _unsetActiveTranslation($translationId)
	{
		// check default translation in settings
		$Setting = ClassRegistry::init('Setting');

		$settingValue = $Setting->getVariable('DEFAULT_TRANSLATION');

		if ($settingValue == $translationId) {
			$Setting->updateVariable('DEFAULT_TRANSLATION', Translation::DEFAULT_TRANSLATION_ID);
		}

		// check translation in cache
		// this is not working, cookie is updated in default set language process in AppController
		// $cookieValue = $this->Cookie->read('Config.translation_id');

		// if ($cookieValue == $translationId) {
		// 	$this->_setLanguageToCookies(Translation::DEFAULT_TRANSLATION_ID);
		// }
	}

	public function delete($id = null)
	{
		$this->title = __('Translations');
		$this->subTitle = __('Delete a Translation');

		$this->_disallowSystemTreanslation($id);

		$this->Crud->on('afterDelete', function(CakeEvent $event) {
			if ($event->subject->success) {
				$this->_unsetActiveTranslation($event->subject->id);
				$this->Translation->clearCache();
			}
		});

		return $this->Crud->execute();
	}

	protected function _disallowSystemTreanslation($id)
	{
		if ($this->Translation->isSystemTranslation($id)) {
			throw new NotFoundException();
		}
	}

	public function downloadTemplate()
	{
		$this->response->file(APP . 'Locale' . DS . 'default.pot', ['name' => 'default.pot', 'download' => true]);

		return $this->response;
	}

	public function download($id)
	{
		$translation = $this->Translation->getTranslation($id);

		if (empty($translation) || $translation['Translation']['id'] == Translation::DEFAULT_TRANSLATION_ID) {
			throw new NotFoundException();
		}

		$Translation = $this->Translation->getItemDataEntity($translation);

		$this->response->file($Translation->getPoFilePath(), ['name' => 'default.po', 'download' => true]);

		return $this->response;
	}
}
