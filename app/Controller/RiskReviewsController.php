<?php
App::uses('ReviewsPlannerController', 'ReviewsPlanner.Controller');

/**
 * @section
 */
class RiskReviewsController extends ReviewsPlannerController
{
	public $components = array(
		// reviews component handles correct model name configuration for CRUD
		// 'ReviewsManager',
		// 'Paginator', 
		'ObjectStatus.ObjectStatus',
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
				],
				'trash' => [
					'enabled' => true
				],
				'history' => [
					'className' => 'ObjectVersion.History',
					'enabled' => true
				],
				'restore' => [
					'className' => 'ObjectVersion.Restore',
					'enabled' => true
				]
			],
			'listeners' => [
				'.RiskReviewsPlanner'
			]
		],
	);

	public $uses = ['RiskReview'];

	public function beforeFilter()
	{
		parent::beforeFilter();
		// $this->Crud->enable('add', 'edit', 'delete', 'index');
	}


}
