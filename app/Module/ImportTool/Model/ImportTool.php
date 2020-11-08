<?php
App::uses('ImportToolAppModel', 'ImportTool.Model');
App::uses('AuthComponent', 'Controller/Component');
App::uses('ImportToolCsv', 'ImportTool.Lib');
App::uses('ImportToolData', 'ImportTool.Lib');
App::uses('ImportToolImport', 'ImportTool.Lib');

class ImportTool extends ImportToolAppModel
{
	public $useTable = false;

	public $actsAs = [
		'FieldData'
	];

	public $validate = [
		'CsvFile' => [
			'extension' => [
				'required' => true,
				'rule' => ['extension', ['csv']],
				'message' => 'The file you uploaded is not a valid CSV file'
			]
		],
		'model' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		]
	];

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('Import Tool');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'CsvFile' => [
				'label' => __('File Upload'),
				'type' => 'file',
				'editable' => true,
				'description' => __('Upload your CSV file here.')
			],
			'model' => [
				'label' => __('Model'),
				'editable' => false,
				'hidden' => true
			],
		];
		
		parent::__construct($id, $table, $ds);
	}

	public function storeImportToolData($data)
	{
		$this->create($data);

		$ret = false;

		if (!$this->validates()) {
			return $ret;
		}

		$fileName = $this->data['ImportTool']['CsvFile']['tmp_name'];
		$modelName = $this->data['ImportTool']['model'];

		$csv = new ImportToolCsv($fileName);
		$data = $csv->getData();
		$errors = $csv->getErrors();

		if (!empty($data) && empty($errors) && !empty($modelName)) {
			// delete previous cached ImportToolData instance
			Cache::delete(self::userPreviewCacheKey('ImportToolData_data'), 'ImportTool');
			Cache::delete(self::userPreviewCacheKey('ImportToolData_model'), 'ImportTool');

			//write ImportToolData to cache
			Cache::write(self::userPreviewCacheKey('ImportToolData_data'), $data, 'ImportTool');
			Cache::write(self::userPreviewCacheKey('ImportToolData_model'), $modelName, 'ImportTool');

			$ret = true;
		}

		return $ret;
	}

	public function getStoredImportToolData()
	{
		$data = Cache::read(self::userPreviewCacheKey('ImportToolData_data'), 'ImportTool');
		$model = Cache::read(self::userPreviewCacheKey('ImportToolData_model'), 'ImportTool');

		return new ImportToolData(ClassRegistry::init($model), $data);
	}

	public function import($data)
	{
		if (empty($data['ImportTool']['checked'])) {
			$this->invalidate('ImportToolData', __('You have to check at least one item to start the import. Please try again.'));
			return false;
		}

		$ImportToolData = $this->getStoredImportToolData();

		if (empty($ImportToolData)) {
			return false;
		}

		$ImportToolImport = new ImportToolImport($ImportToolData);
		$ImportToolImport->setImportRows($data['ImportTool']['checked']);

		$ret = (boolean) $ImportToolImport->saveData();
		if (!$ret) {
			$this->invalidate('ImportToolData', __('Error while saving the data. Please try it again.'));
		}

		return $ret;
	}

	public static function userPreviewCacheKey($name)
	{
		$userId = AuthComponent::user('id');

		return "user_preview_{$name}_{$userId}";
	}
}
