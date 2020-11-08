<?php
App::uses('AttachmentsAppModel', 'Attachments.Model');
App::uses('SidebarWidgetTrait', 'Model/Trait');
App::uses('SystemHealthLib', 'Lib');
App::uses('File', 'Utility');
App::uses('CakeText', 'Utility');

class Attachment extends AttachmentsAppModel
{
	use SidebarWidgetTrait;

	const UPLOADS_DIR = 'files/uploads/';
	const UPLOADS_PATH = WWW_ROOT . self::UPLOADS_DIR;

	const TYPE_NORMAL = 0;
	const TYPE_TMP = 1;

	public static $allowedExtensions = [
		'bmp' => 'image/bmp',
		'gif' => 'image/gif',
		'ief' => 'image/ief',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'png' => 'image/png',
		'x-png' => 'image/png',
		'tiff' => 'image/tiff',
		'psd' => 'image/vnd.adobe.photoshop',
		'dwg' => 'image/vnd.dwg',
		'ico' => 'image/x-icon',
		'pcx' => 'image/x-pcx',
		'pic' => 'image/x-pict',

		'csv' => 'text/csv',
		
		'mp4' => 'video/mp4',
		'mpeg' => 'video/mpeg',
		'ogv' => 'video/ogg',
		'webm' => 'video/webm',
		'f4v' => 'video/x-f4v',
		'avi' => 'video/x-msvideo',

		'doc' => 'application/msword',
		'xls' => 'application/vnd.ms-excel',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
		'ppt' => 'application/vnd.ms-powerpoint',
		'mpp' => 'application/vnd.ms-project',
		'pdf' => 'application/pdf',

		'odt' => 'application/vnd.oasis.opendocument.text',
		'ott' => 'application/vnd.oasis.opendocument.text-template',
		'oth' => 'application/vnd.oasis.opendocument.text-web',
		'odm' => 'application/vnd.oasis.opendocument.text-master',
		'odg' => 'application/vnd.oasis.opendocument.graphics',
		'otg' => 'application/vnd.oasis.opendocument.graphics-template',
		'odp' => 'application/vnd.oasis.opendocument.presentation',
		'otp' => 'application/vnd.oasis.opendocument.presentation-template',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
		'odc' => 'application/vnd.oasis.opendocument.chart',
		'odf' => 'application/vnd.oasis.opendocument.formula',
		'odb' => 'application/vnd.oasis.opendocument.database',
		'odi' => 'application/vnd.oasis.opendocument.image',
		'oxt' => 'application/vnd.openofficeorg.extension',

		'zip' => 'application/zip',
		'tar' => 'application/gzip',
		'msg' => 'application/octet-stream',
		'txt' => 'text/plain',

		'vsdx' => [
			'application/vnd.visio',
			'application/vnd.visio2013',
			'application/octet-stream',
			'application/vnd-ms-visio.drawing',
		]
	];

	public static function getMaxFileSize($type = 'mb')
	{
		$max_post_size = SystemHealthLib::returnBytes(SystemHealthLib::postSize_value());
		$max_file_size = SystemHealthLib::returnBytes(SystemHealthLib::uploadFilesize_value());

		$currentLimit = min($max_post_size, $max_file_size);

		if ($type === 'mb') {
			$currentLimit = $currentLimit / 1024 / 1024;
		}

		return $currentLimit;
	}

	public static function allowedExtensions()
	{
		return array_keys(self::$allowedExtensions);
	}

	public static function allowedMimeTypes()
	{
		$mimeTypes = [];

		foreach (self::$allowedExtensions as $value) {
			if (is_array($value)) {
				$mimeTypes = array_merge($mimeTypes, $value);
			}
			else {
				$mimeTypes[] = $value;
			}
		}

		return $mimeTypes;
	}

	public $displayField = 'name';

	public $actsAs = [
		'Uploader.Attachment' => [
			'file' => [
				'nameCallback' => 'formatName',
				'tempDir' => TMP,
				'uploadDir' => self::UPLOADS_PATH,
				'dbColumn' => 'filename',
				'metaColumns' => [
					'ext' => 'extension',
					'type' => 'mime_type',
					'size' => 'file_size',
				]
			]
		],
		'Uploader.FileValidation' => [
			'file' => [
				// extensions defined in __construct
				//'extension' => [],
				'required' => [
					'rule' => ['required'],
					'message' => 'File required',
				]
			]
		]
	];

	public $belongsTo = [
		'User'
	];

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('Attachment');

		$this->actsAs['Uploader.FileValidation']['file']['extension'] = self::allowedExtensions();
		$this->actsAs['Uploader.FileValidation']['file']['mimeType'] = self::allowedMimeTypes();
		$this->actsAs['Uploader.FileValidation']['file']['filesize'] = self::getMaxFileSize('bytes');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'type' => [
				'label' => __('Type'),
				'editable' => false,
			],
			'hash' => [
				'label' => __('Hash'),
				'editable' => false,
			],
			'model' => [
				'label' => __('Model'),
				'editable' => false,
			],
			'foreign_key' => [
				'label' => __('Foreign Key'),
				'editable' => false,
			],
			'name' => [
				'label' => __('Name'),
				'editable' => false,
			],
			'filename' => [
				'label' => __('File Name'),
				'editable' => false,
			],
			'user_id' => [
				'label' => __('User'),
				'editable' => false,
			],
			'last_created' => [
				'label' => __('Last Created'),
				'editable' => false,
				'hidden' => true
			],
		];

		parent::__construct($id, $table, $ds);
	}

	public function beforeFind($query)
	{
		$query = $this->widgetBeforeFind($query);
		
		return $query;
	}

	public function afterSave($created, $options = [])
	{
		if ($created) {
			// clear the index widget cache when added
			Cache::clearGroup('widget_data', 'widget_data');
		}

		//Project ObjectStatus trigger
		$this->triggerProjectObjectStatus($this->id);

		$this->logAttachment($this->id);
	}

	public function logAttachment($id, $delete = false)
	{
		$models = [
			'VendorAssessments.VendorAssessmentFeedback' => 'VendorAssessments.VendorAssessmentFeedback'
		];

		if (is_array($id)) {
			$data = $id;
		}
		else {
			$data = $this->find('first', [
				'conditions' => [
					"{$this->alias}.id" => $id
				],
				'recursive' => -1
			]);
		}

		if (empty($data) || !isset($models[$data[$this->alias]['model']])) {
			return false;
		}

		$Model = ClassRegistry::init($models[$data[$this->alias]['model']]);

		return $Model->logAttachment($data, $delete);
	}

	/**
	 * Triggers dependent Project statuses.
	 */
	public function triggerProjectObjectStatus($id)
	{
		$data = $this->find('first', [
			'conditions' => [
				'Attachment.id' => $id
			],
			'recursive' => -1
		]);

		$triggerModels = [
			'Project', 'ProjectAchievement', 'ProjectExpense'
		];

		if (empty($data) || !in_array($data['Attachment']['model'], $triggerModels)) {
			return false;
		}

		$Model = ClassRegistry::init($data['Attachment']['model']);

		return $Model->triggerObjectStatus('no_updates', $data['Attachment']['foreign_key']);
	}

	/**
	 * Get attachment data.
	 *
	 * @param  int   $id Attachment ID.
	 * @return array     Data.
	 */
	public function getFile($id)
	{
		return $this->find('first', array(
			'conditions' => array(
				'Attachment.id' => $id
			),
			'recursive' => -1
		));
	}

	/**
	 * Retrieve attachments associated with an item.
	 */
	public function getByItem($model, $foreign_key)
	{
		return $this->find('all', array(
			'conditions' => array(
				'Attachment.model' => $model,
				'Attachment.foreign_key' => $foreign_key
			),
			'order' => array('Attachment.created' => 'DESC'),
			'recursive' => 0
		));
	}

	/**
	 * Clone attachment.
	 */
	public function cloneAttachment($data, $foreignKey)
	{
		$this->create();

		$extension = pathinfo($data['filename'], PATHINFO_EXTENSION);
		$name = $this->formatDisplayName($data['name']);
		$filename = self::hashFileName($name) . '.' . $extension;

		$File = new File(self::UPLOADS_PATH . basename($data['filename']));

		if (!$File->exists()) {
			return false;
		}

		if (!$File->copy(self::UPLOADS_PATH . $filename)) {
			return false;
		}

		$data = [
			'model' => $data['model'],
			'type' => $data['type'],
			'name' => $name,
			'filename' => $filename,
			'extension' => $data['extension'],
			'mime_type' => $data['mime_type'],
			'file_size' => $data['file_size'],
			'description' => $data['description'],
			'user_id' => $data['user_id'],
			'foreign_key' => $foreignKey,
		];

		return $this->save($data);
	}

	public function tmpToNormal($hash, $model, $foreignKey)
	{
		$data = [
			'type' => self::TYPE_NORMAL,
			'hash' => '""',
			'model' => '"' . $model . '"',
			'foreign_key' => $foreignKey
		];

		return $this->updateAll($data, [
			'Attachment.hash' => $hash
		]);
	}

	public function formatName($name, $file)
	{
		return self::hashFileName($name);
	}

	public static function hashFileName($name)
	{
		return CakeText::uuid();
	}

	public function formatDisplayName($filename)
	{
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		$name = pathinfo($filename, PATHINFO_FILENAME);
		$baseName = $name;

		$i = 1;
		while ($this->fileExists($name)) {
			$name = $baseName . '-' . $i;
			$i++;
		}

		return "{$name}.{$extension}";
	}

	public function fileExists($nameNoExt)
	{
		return (boolean) $this->find('count', [
			'conditions' => [
				'Attachment.name LIKE' => $nameNoExt . '.%'
			],
			'recursive' => -1
		]);
	}
}
