<?php
App::uses('IssuesController', 'Controller');

/**
 * @section
 */
class SecurityServiceIssuesController extends IssuesController
{
	public $components = array(
		// reviews component handles correct model name configuration for CRUD
		// 'ReviewsManager',
		// 'Paginator', 
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				],
				'add' => [
					'enabled' => true
				],
				'edit' => [
					'enabled' => true
				],
				'delete' => [
					'enabled' => true
				]
			],
			'listeners' => [
				'Widget.Widget', 
				'.ModuleDispatcher' => [
					'listeners' => [
						'Reports.Reports',
					]
				]
			]
		],
		// 'Visualisation.Visualisation'
	);

	public $uses = ['SecurityServiceIssue'];

	public function beforeFilter()
	{
		parent::beforeFilter();
		// $this->Crud->enable('add', 'edit', 'delete', 'index');
		
		$this->subTitle = __('List of all issues recorded for all your internal controls');
	}


}