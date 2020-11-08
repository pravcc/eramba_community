<?php
App::uses('TranslationsAppModel', 'Translations.Model');
App::uses('CakeText', 'Utility');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('Cache', 'Cache');

class Translation extends TranslationsAppModel
{
	public $displayField = 'name';
	
	public $actsAs = [
		'FieldData.FieldData',
		'Containable',
		'HtmlPurifier.HtmlPurifier' => [
			'config' => 'Strict',
			'fields' => [
				'name',
			]
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'AdvancedFilters.AdvancedFilters',
		'Uploader.Attachment' => [
			'file' => [
				'nameCallback' => 'formatName',
				'uploadDir' => self::TRANSLATIONS_TMP_PATH,
				'dbColumn' => false,
			]
		],
		'Uploader.FileValidation' => [
			'file' => [
				'extension' => ['po'],
				'required' => [
		            'rule' => ['required'],
		            'allowEmpty' => false
        		],
			]
		]
	];

	public $validate = [
		'name' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
		],
	];

	/**
	 * Tmp path for translations upload.
	 */
	const TRANSLATIONS_TMP_PATH = WWW_ROOT . 'files' . DS . 'translations' . DS;

	/**
	 * Default translation values.
	 */
	const DEFAULT_TRANSLATION_ID = 1;
	const DEFAULT_TRANSLATION_NAME = 'eng';

	const STATUS_DISABLED = 0;
	const STATUS_ENABLED = 1;

	public static function statuses($value = null) 
	{
		$options = [
			self::STATUS_DISABLED => __('Disabled'),
			self::STATUS_ENABLED => __('Enabled (Available to select)')
		];

		return parent::enum($value, $options);
	}

	const TYPE_SYSTEM = 0;
	const TYPE_CUSTOM = 1;

	public static function types($value = null) 
	{
		$options = [
			self::TYPE_SYSTEM => __('System'),
			self::TYPE_CUSTOM => __('Custom')
		];

		return parent::enum($value, $options);
	}

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('Translations');
		$this->_group = parent::SECTION_GROUP_SYSTEM;

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'name' => [
				'label' => __('Name'),
				'editable' => true,
				'description' => __('The name of the translation, for example "English". This string will be shown at the login page of eramba as a language option This string will be shown at the login page of eramba as a language option..')
			],
			'folder' => [
				'label' => __('Folder'),
				'editable' => false,
				'hidden' => true,
				'description' => __('')
			],
			'status' => [
				'label' => __('Status'),
				'editable' => true,
				'options' => [$this, 'statuses'],
				'description' => __('If the translation is active or not. Inactive translations are not shown as an option at the login page.')
			],
			'file' => [
				'label' => __('Translation File'),
				'type' => 'file',
				'editable' => true,
				'description' => __('Upload the translated POT file (System / Settings / Languages / Download) that you wish to use for this language - it must be a PO extension. Use this as well to updated languages you have previously uploaded.')
			],
			'type' => [
				'label' => __('Type'),
				'editable' => false,
				'hidden' => true,
				'options' => [$this, 'types'],
				'description' => __('')
			],
		];

		parent::__construct($id, $table, $ds);
	}

	public function getAdvancedFilterConfig()
	{
		$advancedFilterConfig = $this->createAdvancedFilterConfig()
			->group('general', [
				'name' => __('General')
			])
				->nonFilterableField('id')
				->textField('name', [
					'showDefault' => true
				])
				->multipleSelectField('type', [$this, 'types'], [
					'showDefault' => true
				])
				->multipleSelectField('status', [$this, 'statuses'], [
					'showDefault' => true
				]);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function beforeValidate($options = array())
	{
		$this->validate['file']['notBlank'] = [
			'rule' => 'validateFile',
			'required' => true,
			'on' => 'create',
			'message' => __('This file is required')
		];

		$ret = parent::beforeValidate($options);

		if (!empty($this->data['Translation']['id']) && $this->isSystemTranslation($this->data['Translation']['id'])) {
			$this->validate['name']['required'] = false;
			$this->validate['name']['allowEmpty'] = true;
		}
		else {
			$this->validate['name']['required'] = true;
			$this->validate['name']['allowEmpty'] = false;
		}

		return $ret;
	}

	public function validateFile($check)
	{
		return !empty($check['file']);
	}

	public function afterSave($created, $options = array())
	{
		if (!empty($this->id) && !empty($this->data['Translation']['file'])) {
			$this->moveTmpFile($this->id, $this->data['Translation']['file']);
		}
	}

	/**
	 * Move translation file from tmp to Locale folder.
	 * 
	 * @param int $id Translation.id
	 * @param string $tmpFileName Tmp filename.
	 * @return boolean Success.
	 */
	public function moveTmpFile($id, $fileName)
	{
		$ret = true;

		$folderName = self::getCustomTranslationName($id);

		$CustomFolder = new Folder(APP . 'Locale' . DS . $folderName, true);
		$LCFolder = new Folder(APP . 'Locale' . DS . $folderName . DS . 'LC_MESSAGES', true);

		$File = new File(self::TRANSLATIONS_TMP_PATH . $fileName);
		
		$ret &= (bool) $File->copy(APP . 'Locale' . DS . $folderName . DS . 'LC_MESSAGES' . DS . 'default.po', true);
		
		$ret &= (bool) $File->delete();

		return $ret;
	}

	/**
	 * Get custom translation name.
	 * 
	 * @param int $id Translation.id
	 * @return string
	 */
	public static function getCustomTranslationName($id)
	{
		return 'custom_' . $id;
	}

	public function formatName($name, $file)
	{
		return CakeText::uuid();
	}

	/**
	 * Check if translation exists and if translation is enabled.
	 * 
	 * @param int $id Translation.id
	 * @return boolean
	 */
	public function isTranslationAvailable($id)
	{
		return (bool) $this->find('count', [
			'conditions' => [
				'Translation.id' => $id,
				'Translation.status' => self::STATUS_ENABLED
			],
			'recursive' => -1
		]);
	}

	/**
	 * Check if translation is of system type.
	 * 
	 * @param int $id Translation.id
	 * @return boolean
	 */
	public function isSystemTranslation($id)
	{
		return (bool) $this->find('count', [
			'conditions' => [
				'Translation.id' => $id,
				'Translation.type' => self::TYPE_SYSTEM
			],
			'recursive' => -1
		]);
	}

	/**
	 * Get translation record.
	 * 
	 * @param int $id Translation.id
	 * @return array
	 */
	public function getTranslation($id)
	{
		return $this->find('first', [
			'conditions' => [
				'Translation.id' => $id,
			],
			'recursive' => -1
		]);
	}

	/**
	 * Get list of available translations.
	 * 
	 * @return array
	 */
	public function getAvailableTranslations()
	{
		return $this->find('list', [
			'conditions' => [
				'Translation.status' => self::STATUS_ENABLED
			],
			'fields' => [
				'Translation.id', 'Translation.name'
			],
			'recursive' => -1
		]);
	}

	public function clearCache()
	{
		Cache::clear(false, '_cake_core_');
	}
}
