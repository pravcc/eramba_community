<?php
/**
 * @package       AppPreview.Controller
 */
App::uses('AppController', 'Controller');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('TablesHelper', 'Module/LimitlessTheme/View/Helper');

class SectionItemsController extends AppController
{
	public $helpers = array('SectionItems', 'ImportTool.ImportTool', 'Workflows.Workflows', 'UserFields.UserField', 'FieldData.FieldDataCollection', 'FieldData.FieldDataRenderer', 'LimitlessTheme.Tables', 'LimitlessTheme.Buttons', 'LimitlessTheme.Icons');

	public $components = array(
		'Paginator',/* 'Search.Prg', 'AdvancedFilters',*/ 'CustomFields.CustomFieldsMgt',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					// 'className' => 'AdvancedFilters.MultipleFilters',
					'enabled' => true,
					// 'contain' => [
					// 	'HasAndBelongsToMany' => [
					// 		'fields' => ['full_name']
					// 	],
					// 	'HasAndBelongsToMany2' => ['fields' => ['id']],
					// 	'HasMany' => ['fields' => ['id']],
					// 	// 'Comment',
					// 	// 'Attachment',
					// 	'Tag',
					// 	'BelongsTo' => [
					// 		'fields' => ['full_name']
					// 	]
					// ],
					// Filter configuration for index might be disabled this way (same for trash), enabled by default
					// 
					// 'filter' => [
					// 	'enabled' => false
					// ]
				],
				'filter_test' => [
					'enabled' => true,
					'className' => 'AdvancedFilters.MultipleFilters',
					// 'viewVar' => 'data',
					// 'contain' => [
					// 	'HasAndBelongsToMany' => [
					// 		'fields' => ['full_name']
					// 	],
					// 	'HasAndBelongsToMany2' => ['fields' => ['id']],
					// 	'HasMany' => ['fields' => ['id']],
					// 	// 'Comment',
					// 	// 'Attachment',
					// 	'Tag',
					// 	'BelongsTo' => [
					// 		'fields' => ['full_name']
					// 	]
					// ],
				],
			],
			// 'listeners' => ['Api', 'ApiPagination', 'Crud.Search']
		],
		'Visualisation.Visualisation',
		'UserFields.UserFields' => [
			'fields' => ['UserField']
		],
		'Ajax' => array(
			'actions' => array('add', 'edit', 'delete'),
			'modules' => array('comments', 'records', 'attachments')
		),
	);

	/**
	 * Description is in the AppController
	 */
	protected $_appControllerConfig = [
		'components' => [
			'Ajax' => false
		],
		'helpers' => [
		],
		'elements' => [
		]
	];

	public function beforeFilter()
	{
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'trash']);

		parent::beforeFilter();

		$this->title = __('Section Items Title');
		$this->subTitle = __('Sub title test');
		// $this->layout = 'login';

		$this->Auth->allow('filter_test', 'inline_edit');

		// $this->Auth->allow();
		// $this->Auth->authorize = null;
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$AdvancedFiltersListener = $this->Crud->listener('AdvancedFilters');
		
		$this->title = __('Only For Index Custom Title');
		return $this->Crud->execute();
	}

	public function inline_edit($field, $id)
	{
		$this->YoonityJSConnector->deny();
		
		$data = $this->request->data;
		$model = $this->modelClass;

		$data[$model]['id'] = $id;
		
		$this->loadModel($model);
		$this->{$model}->set($data);
		$this->{$model}->id = $id;

		$ret = $this->{$model}->save(null, [
			'fieldList' => array_keys($data[$model])
		]);

		$this->set('item', $this->{$model}->find('first', [
			'conditions' => [
				$model . '.id' => $id
			]
		]));
		$this->set('field', $this->{$model}->getFieldDataEntity($field));
		
		return $this->render('../Elements/inline_edit');
	}

	public function add()
	{
		$this->title = __('Create an item');

		$this->Crud->on('beforeSave', array($this, '_beforeSave'));

		return $this->Crud->execute();
	}

	public function _beforeSave() {
		// debug($this->request->data);
	}

	public function _afterSave() {
		// debug($this->request->data);
		// debug($this->SectionItem->validationErrors);
	}

	public function edit()
	{
		$this->title = __('Edit an item');

		$this->Crud->on('beforeSave', array($this, '_beforeSave'));
		$this->Crud->on('afterSave', array($this, '_afterSave'));

		return $this->Crud->execute();
	}

	public function delete($id = null)
	{
		$this->subTitle = __('Delete an Item');

		return $this->Crud->execute();
	}

	public function filter_test()
	{
		// $this->view = 'index';

		


		
		// exit;
		// debug($values);
		// $requestData = AdvancedFilter::buildValues($values);
		// dd($requestData);
		
		$params = [
			'varchar' => [
				'value' => '123',
				'comparisonType' => AbstractQuery::COMPARISON_EQUAL
			]
		];

		// $params = [
		// 	'varchar' => 'kjhg'
		// ];

		// $this->SectionItem->Behaviors->load('AdvancedFilters.AdvancedFilters');
		// $data = $this->SectionItem->filter('all', $params);
		// dd($data);
// $this->Crud->action()->view('AdvancedFilters./Elements/index');
		// $this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		// $AdvancedFiltersListener = $this->Crud->listener('AdvancedFilters');
		// dd($AdvancedFiltersListener);
		return $this->Crud->execute();
	}

/**
 * example of cutom (independent) use of advanced filters
 */
	public function filterView() {
		$this->title = __('Custom filter view');

		$params = [
			'user_id' => 1,
			'habtm2_id' => [2],
			'habtm2_id__comp_type' => AbstractQuery::COMPARISON_NOT_IN
		];

		$conditions = $this->AdvancedFilters->buildConditions('SectionItem', $params);

		$data = $this->SectionItem->find('all', [
			'fields' => ['SectionItem.id'],
			'conditions' => $conditions,
			'contain' => []
		]);

		debug($data);
		exit;
	}
}
