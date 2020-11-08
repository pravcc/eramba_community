<?php
App::uses('AppController', 'Controller');

class ScopesController extends AppController
{
	public $helpers = [];
	public $components = [
		'Ajax' => [
			'actions' => ['add', 'edit', 'delete'],
			'modules' => []
		],
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'contain' => [
						'CisoRole',
						'CisoDeputy',
						'BoardRepresentative',
						'BoardRepresentativeDeputy',
					]
				],
			],
		],
	];

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
		$this->Crud->enable(['index', 'add', 'edit', 'delete']);

		parent::beforeFilter();

		$this->title = __('System Roles');
		$this->subTitle = false;
	}

	public function index() {
		$this->subTitle = __('System wide roles used for workflows and notifications.');

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->title = __('Delete System Roles');

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Define System Roles');
		$this->initAddEditSubtitle();

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit System Roles');
		$this->initAddEditSubtitle();

		return $this->Crud->execute();
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('System roles are used across the entire system to define workflow and notification settings.');
	}

}
