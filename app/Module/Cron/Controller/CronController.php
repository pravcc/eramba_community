<?php
App::uses('CronAppController', 'Cron.Controller');
App::uses('ErambaCakeEmail', 'Network/Email');
App::uses('AppModel', 'Model');
App::uses('Cron', 'Model');
App::uses('CronModule', 'Cron.Lib');

/**
 * Manages CRON tasks for the app.
 */
class CronController extends CronAppController {
	public $components = array(
		'Session',

		'Crud.Crud' => [
			'actions' => [
				CronModule::ACTION_NAME => [
					'className' => 'Cron.Cron'
				],
				'task' => [
					'className' => 'Cron.CronTask'
				],
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
			],
			'listeners' => [
				'Widget.Widget'
			],
			'eventLogging' => true
		]
	);

	public $uses = array(
		'Cron'
	);

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->autoRender = false;
		$this->Auth->allow(CronModule::ACTION_NAME, 'task');
	}

	public function task($requestId, $task = null)
	{
		$this->Crud->config('eventPrefix', 'Cron');
		$this->layout = 'Cron.cron';

		$this->Crud->addListener('CronUserSession', 'Cron.CronUserSession');

		list($plugin, $class) = pluginSplit($task);

		$this->Crud->addListener($class, $task);

		return $this->Crud->execute();
	}

	/**
	 * Handler that executes all cron jobs.
	 */
	public function job($type, $key) {
		if (Configure::read('Eramba.Settings.CRON_TYPE') !== 'web') {
			echo 'You have to configure Web Cron Type in Settings of Eramba in order to use this.';
			exit;
		}

		$this->Crud->config('eventPrefix', 'Cron');
		$this->layout = 'Cron.cron';

		$this->Crud->addListener('CronSetup', 'Cron.CronSetup', [
			'title' => __('Cron Setup')
		]);
		$this->Crud->addListener('CronUserSession', 'Cron.CronUserSession');

		return $this->Crud->execute();
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');

		$this->title = __('Cron Records');
		$this->subTitle = __('eramba has three crontabs that must run every year, day and hour. This log describes when a cron run and what was the result');

		$this->autoRender = true;

		$this->Crud->on('beforePaginate', [$this, '_beforePaginate']);

		return $this->Crud->execute();
	}

	public function _beforePaginate(CakeEvent $event) {
		$event->subject->paginator->settings['order'] = ['Cron.created' => 'DESC'];
	}

}
