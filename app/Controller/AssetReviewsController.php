<?php
App::uses('ReviewsPlannerController', 'ReviewsPlanner.Controller');

/**
 * @section
 */
class AssetReviewsController extends ReviewsPlannerController
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
				'.AssetReviewsPlanner'
			]
		]
	);

	public $uses = ['AssetReview'];

	public function beforeFilter()
	{
		parent::beforeFilter();
	}
}
