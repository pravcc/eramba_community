<?php
App::uses('Component', 'Controller');
App::uses('Translation', 'Translations.Model');
App::uses('I18n', 'I18n');

class TranslationsComponent extends Component
{
	public $components = ['Cookie'];

	public function __construct(ComponentCollection $collection, $settings = array())
	{
		parent::__construct($collection, array_merge($this->settings, (array)$settings));

		$this->controller = $collection->getController();

		// ensure that cookies are configured
		$this->controller->_setupDefaultCookies();

		// setup language
		$this->_setupLanguage();

		// flush ClassRegistry because wrong translations can be already loaded in models
		ClassRegistry::flush();
	}

	public function initialize(Controller $controller)
	{
		parent::initialize($controller);	
	}

	/**
	 * Read configured translation from cookies or settings and set it as active translation.
	 * 
	 * @return void
	 */
	protected function _setupLanguage()
	{
		$Translation = ClassRegistry::init('Translations.Translation');

		// read active translation from cookies
		$translationId = $this->Cookie->read('Config.translation_id');

		// if we have not active translation read default translation from settings
		if (empty($translationId)) {
			$translationId = Configure::read('Eramba.Settings.DEFAULT_TRANSLATION');
		}

		$translation = $Translation->getTranslation($translationId);

		if (!empty($translation) && $translation['Translation']['status'] == Translation::STATUS_ENABLED) {
			$translation = $Translation->getItemDataEntity($translation);

			$translationId = $translation->getPrimary();
			$lang = $translation->getTranslationName();
		}
		else {
			// fallback
			$translationId = Translation::DEFAULT_TRANSLATION_ID;
			$lang = Translation::DEFAULT_TRANSLATION_NAME;
		}

		// extend translation cookie if its not an api request
		if (!$this->controller->request->is('api')) {
			$this->writeTranslationToCookies($translationId);
		}

		Configure::write('Config.translation_id', $translationId);
		Configure::write('Config.language', $lang);

		// translation is not in l10n catalog (i18n in using this l10n class) so we need to rewrite default fallback
		I18n::getInstance()->l10n->default = $lang;
	}

	/**
	 * Write language to cookies.
	 *
	 * @param int $translationId Translation.id
	 * @return boolean Success.
	 */
	public function writeTranslationToCookies($translationId)
	{
		$this->Cookie->write('Config.translation_id', $translationId);
	}

	/**
	 * Set list of available translations.
	 *
	 * @return void
	 */
	public function setAvailableTranslations()
	{
		$this->controller->set('availableTranslations', ClassRegistry::init('Translations.Translation')->getAvailableTranslations());
	}
}